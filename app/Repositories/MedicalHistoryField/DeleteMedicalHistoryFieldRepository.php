<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;

class DeleteMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute($field){
        if ($field->is_default) {
            return $this->error('Default medical history fields cannot be deleted.', 400);
        }
        
        try {
            $field->delete();
            return $this->success('Medical history field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            return $this->error('Failed to delete medical history field.', 500, $e->getMessage());
        }

    }
}
