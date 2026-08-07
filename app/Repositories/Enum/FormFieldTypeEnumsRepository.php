<?php

namespace App\Repositories\Enum;

use App\Repositories\BaseRepository;
use App\Models\FormFieldType;
use App\Http\Resources\FormFieldTypeResource;

class FormFieldTypeEnumsRepository extends BaseRepository
{
    public function execute()
    {
        $types = FormFieldType::orderBy('id')->get();

        return $this->success(
            'Form field types retrieved successfully.',
            FormFieldTypeResource::collection($types),
            200
        );
    }
}