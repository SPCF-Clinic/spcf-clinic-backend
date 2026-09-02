<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\{
    MedicalHistoryField,
    MedicalHistoryFieldVersion,
    MedicalHistoryFieldOption,
    FormFieldType,
    ActivityLog,
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
                'required_with_field_id' => $validated['required_with_field_id'] ?? null,
                'required_with_field_value' => $validated['required_with_field_value'] ?? null,
                'form_order' => $validated['form_order'],
                'description_text' => $validated['description_text'] ?? null,
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

            ActivityLog::create([
                'group' => 'FORM_FIELD',
                'action' => "New medical history field '{$fieldVersion->field_name}' created.",
                'performed_by' => auth()->id(),
            ]);

            return $this->success('Medical history field created successfully.', new MedicalHistoryFieldResource($baseField), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create medical history field.', 500, $e->getMessage());
        }
    }
}
