<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\{
    FormFieldType,
    PersonalInfoField,
    PersonalInfoFieldVersion,
    PersonalInfoFieldOption,
    ActivityLog,
};
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PersonalInfoFieldResource;

class StorePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($request){
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $formFieldType = FormFieldType::where('name', $validated['type'])->first();

            if (!$formFieldType) {
                throw new \InvalidArgumentException('Invalid form field type.');
            }

            $baseField = PersonalInfoField::create();

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
                        PersonalInfoFieldOption::create([
                            'field_version_id' => $fieldVersion->id,
                            'option_value' => $option,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->error('Failed to create personal info field options.', 500, $e->getMessage());
            }            

            DB::commit();

            $baseField->load('latestVersion.options');

            ActivityLog::create([
                // 'group' => 'FORM_FIELD',
                'action' => "New personal info field '{$fieldVersion->field_name}' created.",
                'performed_by' => auth()->id(),
            ]);

            return $this->success('Personal info field created successfully.', new PersonalInfoFieldResource($baseField), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create personal info field.', 500, $e->getMessage());
        }
    }
}
