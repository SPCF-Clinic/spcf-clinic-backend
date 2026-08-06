<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;

class DeletePersonalInfoFieldRepository extends BaseRepository
{
    public function execute($field)
    {
        if ($field->is_default) {
            return $this->error('Default personal info fields cannot be deleted.', 400);
        }
        
        try {
            $field->delete();
            return $this->success('Personal info field deleted successfully.', null, 200);
        } catch (\Exception $e) {
            return $this->error('Failed to delete personal info field.', 500, $e->getMessage());
        }
    }
}
