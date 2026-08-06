<?php

namespace App\Repositories\MedicalHistoryField;

use App\Repositories\BaseRepository;
use App\Models\MedicalHistoryField;
use App\Http\Resources\MedicalHistoryFieldResource;

class IndexMedicalHistoryFieldRepository extends BaseRepository
{
    public function execute(){
        $fields = MedicalHistoryField::with('latestVersion.formFieldType', 'latestVersion.options')
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
            'Medical history fields retrieved successfully.',
            MedicalHistoryFieldResource::collection($fields),
            200
        );
    }
}
