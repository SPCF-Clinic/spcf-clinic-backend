<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\{
    PersonalInfoField,
};

class IndexPersonalInfoFieldRepository extends BaseRepository
{
    public function execute(){
        $fields = PersonalInfoField::with(['latestVersion.options', 'latestVersion.formFieldType'])->get();

        return $this->success('Personal info fields retrieved successfully.', $fields, 200);
    }
}
