<?php

namespace App\Repositories\Support;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Shared reorder logic for versioned, self-ordering form field models
 * (PersonalInfoField, MedicalHistoryField, ...).
 *
 * Subclasses only need to say which model/resource/column names to use.
 */
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

    public function execute($request)
    {
        $validated = $request->validated();
        $fieldId = (int) $validated['field_id'];
        $targetFormOrder = (int) $validated['target_form_order'];
        $modelClass = $this->modelClass();

        return DB::transaction(function () use ($modelClass, $fieldId, $targetFormOrder) {
            // Lock the row being moved so a concurrent reorder can't read a
            // stale copy of it mid-transaction.
            $selectedField = $modelClass::with('latestVersion')
                ->lockForUpdate()
                ->find($fieldId);

            if (! $selectedField) {
                return $this->error($this->notFoundMessage(), 404);
            }

            if ($selectedField->is_default) {
                return $this->error($this->defaultLockedMessage(), 400);
            }

            // Lock the whole reorderable set for the duration of the
            // transaction. Without this, two simultaneous reorder requests
            // can both read the same "before" ordering and each commit a
            // conflicting set of form_order values.
            $orderedFields = $this->reorderableQuery($modelClass)
                ->lockForUpdate()
                ->get()
                ->sortBy($this->sortCriteria())
                ->values();

            $currentIndex = $orderedFields->search(fn ($field) => $field->id === $selectedField->id);

            if ($currentIndex === false) {
                return $this->error($this->notReorderableMessage(), 422);
            }

            $targetFormOrder = max(1, min($targetFormOrder, $orderedFields->count()));

            $reorderedFields = $orderedFields
                ->reject(fn ($field) => $field->id === $selectedField->id)
                ->values()
                ->all();

            array_splice($reorderedFields, $targetFormOrder - 1, 0, [$selectedField]);

            foreach (array_values($reorderedFields) as $index => $field) {
                $field->latestVersion?->update([
                    'form_order' => $index + 1,
                ]);
            }

            $updatedFields = $this->reorderableQuery($modelClass)
                ->with('latestVersion.formFieldType', 'latestVersion.options')
                ->get()
                ->sortBy($this->sortCriteria())
                ->values();

            $resourceClass = $this->resourceClass();

            return $this->success(
                'Form order updated successfully.',
                $resourceClass::collection($updatedFields),
                200
            );
        });
    }

    /**
     * Base query for "fields that participate in top-level reordering":
     * non-default, and not conditionally dependent on another field.
     */
    protected function reorderableQuery(string $modelClass)
    {
        return $modelClass::with('latestVersion')
            ->where('is_default', false)
            ->whereHas('latestVersion', function ($query) {
                $query->whereNull($this->requiredWithColumn());
            });
    }

    /**
     * Sort by numeric form_order (nulls last), tie-broken by id.
     * Using numeric comparisons here (rather than zero-padded string
     * concatenation) avoids sort-key corruption when form_order is null,
     * since PHP_INT_MAX has more digits than the padding width can hide.
     */
    protected function sortCriteria(): array
    {
        return [
            fn ($field) => $field->latestVersion?->form_order ?? PHP_INT_MAX,
            fn ($field) => $field->id,
        ];
    }
}
