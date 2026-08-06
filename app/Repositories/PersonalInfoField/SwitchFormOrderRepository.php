<?php

namespace App\Repositories\PersonalInfoField;

use App\Http\Resources\PersonalInfoFieldResource;
use App\Models\PersonalInfoField;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class SwitchFormOrderRepository extends BaseRepository
{
    public function execute($request)
    {
        $validated = $request->validated();
        $fieldId = (int) $validated['field_id'];
        $targetFormOrder = (int) $validated['target_form_order'];

        return DB::transaction(function () use ($fieldId, $targetFormOrder) {
            $selectedField = PersonalInfoField::with('latestVersion')
                ->find($fieldId);

            if (! $selectedField) {
                return $this->error('The selected personal info field could not be found.', 404);
            }

            if ($selectedField->is_default) {
                return $this->error('Default personal info fields cannot be reordered.', 400);
            }

            $orderedFields = PersonalInfoField::with('latestVersion')
                ->whereHas('latestVersion', function ($query) {
                    $query->whereNull('required_with_field_id');
                })
                ->where('is_default', false)
                ->get()
                ->sortBy(function ($field) {
                    $order = $field->latestVersion?->form_order ?? PHP_INT_MAX;

                    return str_pad((string) $order, 10, '0', STR_PAD_LEFT)
                        . '-' . str_pad((string) $field->id, 10, '0', STR_PAD_LEFT);
                })
                ->values();

            $currentIndex = $orderedFields->search(fn ($field) => $field->id === $selectedField->id);

            if ($currentIndex === false) {
                return $this->error('Personal info fields are not available for reordering.', 422);
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

            $updatedFields = PersonalInfoField::with('latestVersion.formFieldType', 'latestVersion.options')
                ->whereHas('latestVersion', function ($query) {
                    $query->whereNull('required_with_field_id');
                })
                ->where('is_default', false)
                ->get()
                ->sortBy(function ($field) {
                    $order = $field->latestVersion?->form_order ?? PHP_INT_MAX;

                    return str_pad((string) $order, 10, '0', STR_PAD_LEFT)
                        . '-' . str_pad((string) $field->id, 10, '0', STR_PAD_LEFT);
                })
                ->values();

            return $this->success(
                'Form order updated successfully.',
                PersonalInfoFieldResource::collection($updatedFields),
                200
            );
        });

    }
}
