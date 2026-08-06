<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\PersonalInfoField;
use App\Http\Resources\PersonalInfoFieldResource;

class IndexPersonalInfoFieldRepository extends BaseRepository
{
    public function execute(){
        $fields = PersonalInfoField::with('latestVersion.formFieldType', 'latestVersion.options')
            ->whereHas('latestVersion', function ($query) {
                $query->where('required_with_field_id', '=', null);
            })
            ->get();

        return $this->success(
            'Personal info fields retrieved successfully.',
            PersonalInfoFieldResource::collection($fields),
            200
        );
    }
}
