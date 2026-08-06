<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\MedicalHistoryField;
use App\Http\Resources\MedicalHistoryFieldResource;

class IndexMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute(){
        $fields = MedicalHistoryField::with('latestVersion.formFieldType', 'latestVersion.options')->get();

        return $this->success(
            'Medical history fields retrieved successfully.',
            MedicalHistoryFieldResource::collection($fields),
            200
        );
    }
}
