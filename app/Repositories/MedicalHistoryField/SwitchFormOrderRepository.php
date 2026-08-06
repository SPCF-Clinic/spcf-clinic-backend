<?php

namespace App\Repositories\MedicalHistoryField;

use App\Http\Resources\MedicalHistoryFieldResource;
use App\Models\{
    MedicalHistoryField,
    MedicalHistoryFieldVersion,
};
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
            $selectedField = MedicalHistoryField::with('latestVersion')
                ->find($fieldId);

            if (! $selectedField) {
                return $this->error('The selected medical history field could not be found.', 404);
            }

            if ($selectedField->is_default) {
                return $this->error('Default medical history fields cannot be reordered.', 400);
            }

            $orderedFields = MedicalHistoryField::with('latestVersion')
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
                return $this->error('Medical history fields are not available for reordering.', 422);
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

            $updatedFields = MedicalHistoryField::with('latestVersion.formFieldType', 'latestVersion.options')
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
                MedicalHistoryFieldResource::collection($updatedFields),
                200
            );
        });
    }
}
