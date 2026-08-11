<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\{PersonalInfoField, ActivityLog};
use Illuminate\Support\Facades\DB;

class DeletePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($request, $field)
    {
        if ($field->is_default) {
            return $this->error('Default personal info fields cannot be deleted.', 400);
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $baseField = PersonalInfoField::with('latestVersion')->lockForUpdate()->findOrFail($field->id);
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
                'action' => "Personal info field '{$latestVersion?->field_name}' deleted.",
                'performed_by' => auth()->id(),
            ]);

            $baseField->delete();

            DB::commit();

            return $this->success('Personal info field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to delete personal info field.', 500, $e->getMessage());
        }
    }
}