<?php

namespace App\Repositories\MedicalHistoryField;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\MedicalHistoryFieldResource;
use App\Models\MedicalHistoryField;
use App\Models\MedicalHistoryFieldVersion;
use App\Repositories\BaseRepository;
use App\Support\FormFieldConflictChecker;
use App\Support\FormVersion;

class ReorderFormRepository extends BaseRepository
{
    public function execute($request)
    {
        DB::beginTransaction();

        try {
            if (FormVersion::compute(MedicalHistoryField::class) !== $request->input('form_version')) {
                return $this->error('Stale form version provided.', 400);
            }

            $fields = $request->input('fields');
            $formVersion = $request->input('form_version');

            $defaultFields = MedicalHistoryField::where('is_default', true)->pluck('id')->toArray();
            $defaultFieldFormOrders = MedicalHistoryFieldVersion::whereIn('medical_history_field_id', $defaultFields)->pluck('form_order')->toArray();

            foreach ($fields as $field) {
                if (in_array($field['field_id'], $defaultFields)) {
                    return $this->error('Default medical history fields cannot be reordered.', 400);
                }
                if (in_array($field['form_order'], $defaultFieldFormOrders)) {
                    return $this->error('Target form order is occupied by a default field.', 400);
                }

                $medicalHistoryField = MedicalHistoryField::find($field['field_id']);
                $latestVersion = $medicalHistoryField->latestVersion;

                if ($latestVersion->form_order !== $field['form_order']) {
                    $latestVersion->update(['form_order' => $field['form_order']]);
                }
            }

            $conflicts = FormFieldConflictChecker::checkForConflicts(MedicalHistoryField::class);
            if (!empty($conflicts)) {
                DB::rollBack();
                return $this->error('Form order conflicts detected:', 400, $conflicts);
            }

            DB::commit();

            return $this->success('Medical history fields reordered successfully.', MedicalHistoryFieldResource::collection(MedicalHistoryField::all()), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while reordering medical history fields.', 500, $e->getMessage());
        }
    }
}