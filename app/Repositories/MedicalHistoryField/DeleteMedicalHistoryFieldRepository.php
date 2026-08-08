<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;

class DeleteMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute($field){
        if ($field->is_default) {
            return $this->error('Default medical history fields cannot be deleted.', 400);
        }
        
        try {
            ActivityLog::create([
                'group' => 'FORM_FIELD',
                'action' => "Medical history field '{$field->latestVersion?->field_name}' deleted.",
                'performed_by' => auth()->id(),
            ]);
            
            $field->delete();
            return $this->success('Medical history field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            return $this->error('Failed to delete medical history field.', 500, $e->getMessage());
        }

    }
}
