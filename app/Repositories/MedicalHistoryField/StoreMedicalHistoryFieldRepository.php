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
use App\Support\FormOrderInserter;

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

            $targetFormOrder = $validated['form_order'];
            $existingDefaultField = MedicalHistoryFieldVersion::where('form_order', $targetFormOrder)
                ->whereHas('medicalHistoryField', function ($query) {
                    $query->where('is_default', true);
                })
                ->first();
            if ($existingDefaultField) {
                throw new \InvalidArgumentException('Cannot insert a new field at the same form order as an existing default field.');
            }

            $level = isset($validated['required_with_field_id']) && $validated['required_with_field_id'] !== null ? 'additional' : 'parent';
            if ($level === 'additional') {
                $targetOption = isset($validated['required_with_option_id']) ? MedicalHistoryFieldOption::find($validated['required_with_option_id']) : null;
                if ($targetOption?->option_value != $validated['required_with_field_value']) {
                    throw new \InvalidArgumentException('The specified option\'s value does not match the required field value.');
                }
                $parentField = MedicalHistoryField::find($validated['required_with_field_id']);
                $parentFieldVersion = $parentField->latestVersion;
                if (!$parentFieldVersion) {
                    throw new \InvalidArgumentException('The specified parent field does not exist.');
                }

                $siblingFieldVersions = MedicalHistoryFieldVersion::where('required_with_field_id', $parentField->id)->get();

                $targetFormOrder = $parentFieldVersion->form_order + $siblingFieldVersions->count() + 1;

                FormOrderInserter::makeRoomAt(MedicalHistoryFieldVersion::class, $targetFormOrder);
            } else {
                if (MedicalHistoryFieldVersion::where('form_order', $targetFormOrder)->exists()) {
                    FormOrderInserter::makeRoomAt(MedicalHistoryFieldVersion::class, $targetFormOrder);
                }
            }

            $baseField = MedicalHistoryField::create();

            $fieldVersion = $baseField->versions()->create([
                'version_number' => 1,
                'field_name' => $validated['name'],
                'form_field_type_id' => $formFieldType->id,
                'is_required' => $validated['is_required'] ?? false,
                'required_with_field_id' => $validated['required_with_field_id'] ?? null,
                'required_with_field_value' => $validated['required_with_field_value'] ?? null,
                'required_with_option_id' => $validated['required_with_option_id'] ?? null,
                'form_order' => $targetFormOrder,
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
            
            ActivityLog::create([
                'group' => 'FORM_FIELD',
                'action' => "New medical history field '{$fieldVersion->field_name}' created.",
                'performed_by' => auth()->id(),
                ]);
                
            DB::commit();

            $baseField->load('latestVersion.options');

            return $this->success('Medical history field created successfully.', new MedicalHistoryFieldResource($baseField), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create medical history field.', 500, $e->getMessage());
        }
    }
}
