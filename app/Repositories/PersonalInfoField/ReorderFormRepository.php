<?php

namespace App\Repositories\PersonalInfoField;

use App\Http\Resources\PersonalInfoFieldResource;
use App\Models\PersonalInfoField;
use App\Repositories\BaseRepository;

class ReorderFormRepository extends BaseRepository
{
    public function execute($request)
    {
        $fields = $request->input('fields');
        $formVersion = $request->input('form_version');

        foreach ($fields as $field) {
            if (PersonalInfoField::where('id', $field['field_id'])->value('is_default')) {
                return $this->error('Default personal info fields cannot be reordered.', null, 400);
            }

            $personalInfoField = PersonalInfoField::find($field['field_id']);
            $latestVersion = $personalInfoField->latestVersion;

            if ($latestVersion->form_order !== $field['form_order']) {
                $latestVersion->update(['form_order' => $field['form_order'], 'form_version' => $formVersion]);
            }
        }

        return $this->success('Personal info fields reordered successfully.', PersonalInfoFieldResource::collection(PersonalInfoField::all()), 200);
    }
}