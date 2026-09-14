<?php

namespace App\Repositories\Support;

use App\Repositories\BaseRepository;
use App\Support\FormVersion;
use App\Support\FormFieldConflictResolver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

abstract class AbstractReorderFormRepository extends BaseRepository
{
    abstract protected function modelClass(): string;

    abstract protected function resourceClass(): string;

    /**
     * Column on the *version* table that flags a field as conditionally
     * dependent on another field (e.g. 'required_with_field_id').
     */
    abstract protected function requiredWithColumn(): string;

    abstract protected function notFoundMessage(): string;

    abstract protected function defaultLockedMessage(): string;

    abstract protected function notReorderableMessage(): string;

    /**
     * Shown when the request's form_version doesn't match the form's
     * current fingerprint — i.e. the form changed (a field was added,
     * edited, deleted, or reordered) since the client last loaded it.
     */
    protected function staleFormVersionMessage(): string
    {
        return 'This form has changed since you last loaded it. Please refresh the page and try again.';
    }

    /**
     * Shown when the requested target_form_order is a value currently held
     * by a default (locked) field. Override for model-specific wording.
     */
    protected function defaultPositionMessage(): string
    {
        return 'That position is occupied by a default field, and default fields cannot be reordered.';
    }

    /**
     * Shown when a conditional field is targeted at a form_order outside
     * the set currently held by its own siblings (fields sharing the same
     * parent). Override for model-specific wording.
     */
    protected function conditionalFieldLockedMessage(): string
    {
        return 'Only the parent field can be moved; changes to its ordering will also reorder its conditional '
            .'fields. This field can only be reordered relative to other conditional fields under the same parent.';
    }

    public function execute($request)
    {
        $validated = $request->validated();
        $fieldId = (int) $validated['field_id'];
        $targetFormOrder = (int) $validated['target_form_order'];
        $submittedFormVersion = $validated['form_version'];
        $modelClass = $this->modelClass();
        $requiredWithColumn = $this->requiredWithColumn();

        return DB::transaction(function () use ($modelClass, $requiredWithColumn, $fieldId, $targetFormOrder, $submittedFormVersion) {
            // Lock every field of this type for the duration of the
            // transaction so a concurrent reorder can't read a stale
            // ordering.
            $modelClass::lockForUpdate()->get();

            if (FormVersion::compute($modelClass) !== $submittedFormVersion) {
                return $this->error($this->staleFormVersionMessage(), 409);
            }

            $selectedField = $modelClass::with('latestVersion')->find($fieldId);

            if (! $selectedField) {
                return $this->error($this->notFoundMessage(), 404);
            }

            if ($selectedField->is_default) {
                return $this->error($this->defaultLockedMessage(), 400);
            }

            $parentId = $selectedField->latestVersion?->{$requiredWithColumn};

            if ($parentId !== null) {
                return $this->reorderConditionalField($selectedField, $targetFormOrder);
            }

            return $this->reorderParentField($selectedField, $targetFormOrder);
        });
    }

    /**
     * Move a parent field (and its conditional fields, which follow it as
     * one block) to position $targetFormOrder.
     */
    protected function reorderParentField($selectedField, int $targetFormOrder)
    {
        $modelClass = $this->modelClass();
        $requiredWithColumn = $this->requiredWithColumn();

        $reorderDirection = null;
        $originalFormOrder = $selectedField->latestVersion?->form_order;
        if ($originalFormOrder > $targetFormOrder) {
            $reorderDirection = 'backward';
        } elseif ($originalFormOrder < $targetFormOrder) {
            $reorderDirection = 'forward';
        }

        $targetIsDefaultPosition = $modelClass::where('is_default', true)
            ->whereHas('latestVersion', fn ($query) => $query->where('form_order', $targetFormOrder))
            ->exists();

        if ($targetIsDefaultPosition) {
            return $this->error($this->defaultPositionMessage(), 400);
        }

        // Conditional fields attached to this parent, in their existing
        // relative order — these move together with the parent.
        $children = $modelClass::with('latestVersion')
            ->whereHas('latestVersion', fn ($query) => $query->where($requiredWithColumn, $selectedField->id))
            ->get()
            ->sortBy(fn ($field) => $field->latestVersion?->form_order ?? PHP_INT_MAX)
            ->values();

        $offset = $children->count();

        // The moving block occupies x, x+1, ..., x+offset.
        $selectedField->latestVersion?->update(['form_order' => $targetFormOrder]);

        foreach ($children as $index => $child) {
            $child->latestVersion?->update(['form_order' => $targetFormOrder + $index + 1]);
        }

        // Every other movable field shifts to make room, keeping its relative order, packed
        // in starting right after the moved block's new range. Default
        // fields are never touched, and fields whose original form_order
        // was below x are left exactly as they were.
        $movedIds = $children->pluck('id')->push($selectedField->id);

        if ($reorderDirection === 'backward') {
            $others = $modelClass::with('latestVersion')
                ->where('is_default', false)
                ->whereNotIn('id', $movedIds)
                ->get()
                ->filter(fn ($field) => $field->latestVersion?->form_order !== null
                    && $field->latestVersion->form_order >= $targetFormOrder)
                ->sortBy(fn ($field) => $field->latestVersion->form_order)
                ->values();
            
            $next = $targetFormOrder + $offset + 1;
            foreach ($others as $field) {
                $field->latestVersion?->update(['form_order' => $next]);
                $next++;
            }
        } else {
            $others = $modelClass::with('latestVersion')
                ->where('is_default', false)
                ->whereNotIn('id', $movedIds)
                ->get()
                ->filter(fn ($field) => $field->latestVersion?->form_order !== null
                    && $field->latestVersion->form_order <= $targetFormOrder)
                ->sortBy(fn ($field) => $field->latestVersion->form_order)
                ->values();
            
            $next = $targetFormOrder - $offset;
            foreach ($others as $field) {
                $field->latestVersion?->update(['form_order' => $next]);
                $next--;
            }
        }

        return $this->respondWithUpdatedFields($movedIds->all(), $targetFormOrder);
    }

    /**
     * Move a conditional field to position $targetFormOrder, relative to
     * its own siblings (other conditional fields under the same parent)
     * only.
     */
    protected function reorderConditionalField($selectedField, int $targetFormOrder)
    {
        $parentField = $selectedField->latestVersion?->requiredWithField;

        if (! $parentField) {
            return $this->error($this->notReorderableMessage(), 422);
        }

        $modelClass = $this->modelClass();
        $requiredWithColumn = $this->requiredWithColumn();

        $originalFormOrder = $selectedField->latestVersion?->form_order;
        $reorderDirection = null;
        if ($originalFormOrder > $targetFormOrder) {
            $reorderDirection = 'backward';
        } elseif ($originalFormOrder < $targetFormOrder) {
            $reorderDirection = 'forward';
        }

        $siblings = $modelClass::with('latestVersion')
            ->whereHas('latestVersion', fn ($query) => $query->where($requiredWithColumn, $parentField->id))
            ->get();

        $siblingValues = $siblings
            ->pluck('latestVersion.form_order')
            ->filter(fn ($value) => $value !== null)
            ->values();

        if (! $siblingValues->contains($targetFormOrder)) {
            return $this->error($this->conditionalFieldLockedMessage(), 422);
        }

        // Every OTHER sibling shifts by exactly one slot to make room. Update from highest to
        // lowest so no two rows ever briefly hold the same value. The
        // selected field then takes x itself.

        if ($reorderDirection === 'backward') {
            $others = $siblings
                ->reject(fn ($field) => $field->id === $selectedField->id)
                ->filter(fn ($field) => $field->latestVersion?->form_order !== null
                    && $field->latestVersion->form_order >= $targetFormOrder)
                ->sortByDesc(fn ($field) => $field->latestVersion->form_order)
                ->values();

            foreach ($others as $field) {
                $field->latestVersion?->update(['form_order' => $field->latestVersion->form_order + 1]);
            }
        } else {
            $others = $siblings
                ->reject(fn ($field) => $field->id === $selectedField->id)
                ->filter(fn ($field) => $field->latestVersion?->form_order !== null
                    && $field->latestVersion->form_order <= $targetFormOrder)
                ->sortBy(fn ($field) => $field->latestVersion->form_order)
                ->values();

            foreach ($others as $field) {
                $field->latestVersion?->update(['form_order' => $field->latestVersion->form_order - 1]);
            }
        }

        $selectedField->latestVersion?->update(['form_order' => $targetFormOrder]);

        return $this->respondWithUpdatedFields([$selectedField->id], $targetFormOrder);
    }

    /**
     * Re-fetch and return the full form, shaped exactly like the index
     * response: top-level (parent) fields only — conditional fields are
     * nested inside each one via the resource's `additional_fields` — sorted
     * by form_order.
     */
    protected function respondWithUpdatedFields(array $ignoreFieldIds = [], ?int $targetFormOrder = null)
    {
        FormFieldConflictResolver::resolve($this->modelClass());

        $modelClass = $this->modelClass();
        $resourceClass = $this->resourceClass();
        $requiredWithColumn = $this->requiredWithColumn();

        $updatedFields = $modelClass::with('latestVersion.formFieldType', 'latestVersion.options')
            ->whereHas('latestVersion', function ($query) use ($requiredWithColumn) {
                $query->where($requiredWithColumn, '=', null);
            })
            ->get();

        $updatedFields = $this->sortFields($updatedFields);

        return $this->success(
            'Form order updated successfully.',
            $resourceClass::collection($updatedFields),
            200
        );
    }

    /**
     * Sort by numeric form_order (nulls last), tie-broken by id.
     */
    protected function sortFields(Collection $fields): Collection
    {
        return $fields
            ->sortBy(fn ($field) => $field->id)
            ->sortBy(fn ($field) => $field->latestVersion?->form_order ?? PHP_INT_MAX)
            ->values();
    }
}