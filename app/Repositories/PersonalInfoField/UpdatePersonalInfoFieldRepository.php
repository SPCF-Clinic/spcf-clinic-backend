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
use App\Http\Resources\PersonalInfoFieldResource;

class UpdatePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($request, $field)
    {
        if ($field->is_default) {
            return $this->error('Default personal info fields cannot be updated.', 400);
        }
        
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            if (isset($validated['type'])) {
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
                'required_with_field_id' => $validated['required_with_field_id'] ?? $latestVersion->required_with_field_id,
                'required_with_field_value' => $validated['required_with_field_value'] ?? $latestVersion->required_with_field_value,
                'form_order' => $latestVersion->form_order,
                'description_text' => $validated['description_text'] ?? $latestVersion->description_text
            ]);

            $latestVersion->update([
                'form_order' => null
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

            return $this->success('Personal info field updated successfully.', new PersonalInfoFieldResource($baseField), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update personal info field.', 500, $e->getMessage());
        }
    }
}
