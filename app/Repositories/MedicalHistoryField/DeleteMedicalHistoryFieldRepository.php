<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\{MedicalHistoryField, ActivityLog};
use App\Support\FormOrderCompactor;
use Illuminate\Support\Facades\DB;

class DeleteMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute($request, $field)
    {
        if ($field->is_default) {
            return $this->error('Default medical history fields cannot be deleted.', 400);
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $baseField = MedicalHistoryField::with('latestVersion')->lockForUpdate()->findOrFail($field->id);
            $latestVersion = $baseField->latestVersion;

            if ((int) $validated['version_number'] !== (int) $latestVersion->version_number) {
                DB::rollBack();
                return $this->error(
                    'This field has changed since you last loaded it. Please refresh the page and try again.',
                    409
                );
            }

            ActivityLog::create([
                'group' => 'FORM_FIELD',
                'action' => "Medical history field '{$latestVersion?->field_name}' deleted.",
                'performed_by' => auth()->id(),
            ]);

            $baseField->delete();

            FormOrderCompactor::compact(MedicalHistoryField::class);

            DB::commit();

            return $this->success('Medical history field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to delete medical history field.', 500, $e->getMessage());
        }
    }
}