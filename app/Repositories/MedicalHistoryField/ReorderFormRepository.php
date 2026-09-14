<?php

namespace App\Repositories\MedicalHistoryField;

use App\Http\Resources\MedicalHistoryFieldResource;
use App\Models\MedicalHistoryField;
use App\Repositories\BaseRepository;

class ReorderFormRepository extends BaseRepository
{
    public function execute($request)
    {
        $fields = $request->input('fields');
        $formVersion = $request->input('form_version');

        foreach ($fields as $field) {
            if (MedicalHistoryField::where('id', $field['field_id'])->value('is_default')) {
                return $this->error('Default medical history fields cannot be reordered.', null, 400);
            }

            $medicalHistoryField = MedicalHistoryField::find($field['field_id']);
            $latestVersion = $medicalHistoryField->latestVersion;

            if ($latestVersion->form_order !== $field['form_order']) {
                $latestVersion->update(['form_order' => $field['form_order'], 'form_version' => $formVersion]);
            }
        }

        return $this->success('Medical history fields reordered successfully.', MedicalHistoryFieldResource::collection(MedicalHistoryField::all()), 200);
    }
}