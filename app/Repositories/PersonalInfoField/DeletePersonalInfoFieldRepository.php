<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;

class DeletePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($field)
    {
        if ($field->is_default) {
            return $this->error('Default personal info fields cannot be deleted.', 400);
        }
        
        try {
            ActivityLog::create([
                'group' => 'FORM_FIELD',
                'action' => "Personal info field '{$field->latestVersion?->field_name}' deleted.",
                'performed_by' => auth()->id(),
            ]);
            
            $field->delete();
            return $this->success('Personal info field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            return $this->error('Failed to delete personal info field.', 500, $e->getMessage());
        }
    }
}
