<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\{
    MedicalHistoryField,
    MedicalHistoryFieldVersion,
};

class SwitchFormOrderRepository extends BaseRepository
{
    public function execute($request){
        $validated = $request->validated();

        $field1 = MedicalHistoryField::findOrFail($validated['field_id_1']);
        $field2 = MedicalHistoryField::findOrFail($validated['field_id_2']);

        if ($field1->is_default || $field2->is_default) {
            return $this->error('Default medical history fields cannot be reordered.', 400);
        }

        $version1 = $field1->latestVersion;
        $version2 = $field2->latestVersion;

        // Swap the form_order values
        $tempOrder = $version1->form_order;
        $version1->form_order = $version2->form_order;
        $version2->form_order = $tempOrder;

        // Save the changes
        $version1->save();
        $version2->save();

        return response()->json([
            'message' => 'Form order switched successfully.',
            'data' => [
                'field_1' => new MedicalHistoryFieldResource($field1),
                'field_2' => new MedicalHistoryFieldResource($field2),
            ],
        ]);
    }
}
