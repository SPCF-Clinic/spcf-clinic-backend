<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\{
    PersonalInfoFieldVersion,
    PersonalInfoFieldOption,
    PersonalInfoField,
    FormFieldType,
};
use Illuminate\Support\Facades\DB;

class UpdatePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($request, $field)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            if ($validated['type']) {
                $formFieldType = FormFieldType::where('name', $validated['type'])->first();
            } else {
                $formFieldType = $field->latestVersion->formFieldType;
            }
            

            if (!$formFieldType) {
                throw new \InvalidArgumentException('Invalid form field type.');
            }

            $baseField = PersonalInfoField::findOrFail($field->id);
            $latestVersion = $baseField->latestVersion;

            $newVersionNumber = $latestVersion->version_number + 1;

            $newVersion = $baseField->versions()->create([
                'version_number' => $newVersionNumber,
                'field_name' => $validated['name'] ?? $latestVersion->field_name,
                'form_field_type_id' => $formFieldType->id,
                'is_required' => $validated['is_required'] ?? $latestVersion->is_required,
            ]);

            try {
                if ($formFieldType->has_options) {
                    if (!empty($validated['options'])) {
                        foreach ($validated['options'] as $option) {
                            PersonalInfoFieldOption::create([
                                'field_version_id' => $newVersion->id,
                                'option_value' => $option,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->error('Failed to create personal info field options.', 500, $e->getMessage());
            }

            DB::commit();

            $baseField->load('latestVersion.options');

            return $this->success('Personal info field updated successfully.', $baseField, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update personal info field.', 500, $e->getMessage());
        }
    }
}
