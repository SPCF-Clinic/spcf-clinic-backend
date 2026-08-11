<?php

namespace App\Repositories\PersonalInfoField;

use App\Repositories\BaseRepository;
use App\Models\PersonalInfoField;
use App\Http\Resources\PersonalInfoFieldResource;
use App\Support\FormVersion;

class IndexPersonalInfoFieldRepository extends BaseRepository
{
    public function execute(){
        $fields = PersonalInfoField::with('latestVersion.formFieldType', 'latestVersion.options')
            ->whereHas('latestVersion', function ($query) {
                $query->where('required_with_field_id', '=', null);
            })
            ->get()
            ->sortBy(function ($field) {
                $order = $field->latestVersion?->form_order ?? PHP_INT_MAX;

                return str_pad((string) $order, 10, '0', STR_PAD_LEFT)
                    . '-' . str_pad((string) $field->id, 10, '0', STR_PAD_LEFT);
            })
            ->values();

        return $this->success(
            'Personal info fields retrieved successfully.',
            PersonalInfoFieldResource::collection($fields),
            200,
            ['form_version' => FormVersion::compute(PersonalInfoField::class)]
        );
    }
}