<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\{
    MedicalHistoryField,
    MedicalHistoryFieldVersion,
    MedicalHistoryFieldOption,
    FormFieldType,
};
use Illuminate\Support\Facades\DB;
use App\Http\Resources\MedicalHistoryFieldResource;

class StoreMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute($request){
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $formFieldType = FormFieldType::where('name', $validated['type'])->first();

            if (!$formFieldType) {
                throw new \InvalidArgumentException('Invalid form field type.');
            }

            $baseField = MedicalHistoryField::create();

            $fieldVersion = $baseField->versions()->create([
                'version_number' => 1,
                'field_name' => $validated['name'],
                'form_field_type_id' => $formFieldType->id,
                'is_required' => $validated['is_required'] ?? false,
            ]);

            try {
                if ($formFieldType->has_options && !empty($validated['options'])) {
                    foreach ($validated['options'] as $option) {
                        MedicalHistoryFieldOption::create([
                            'field_version_id' => $fieldVersion->id,
                            'option_value' => $option,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->error('Failed to create medical history field options.', 500, $e->getMessage());
            }

            DB::commit();

            $baseField->load('latestVersion.options');

            return $this->success('Medical history field created successfully.', new MedicalHistoryFieldResource($baseField), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create medical history field.', 500, $e->getMessage());
        }
    }
}
