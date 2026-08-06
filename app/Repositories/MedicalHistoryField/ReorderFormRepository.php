<?php

namespace App\Repositories\MedicalHistoryField;

use App\Http\Resources\MedicalHistoryFieldResource;
use App\Models\MedicalHistoryField;
use App\Repositories\Support\AbstractReorderFormRepository;

class ReorderFormRepository extends AbstractReorderFormRepository
{
    protected function modelClass(): string
    {
        return MedicalHistoryField::class;
    }

    protected function resourceClass(): string
    {
        return MedicalHistoryFieldResource::class;
    }

    protected function requiredWithColumn(): string
    {
        return 'required_with_field_id';
    }

    protected function notFoundMessage(): string
    {
        return 'The selected medical history field could not be found.';
    }

    protected function defaultLockedMessage(): string
    {
        return 'Default medical history fields cannot be reordered.';
    }

    protected function notReorderableMessage(): string
    {
        return 'Medical history fields are not available for reordering.';
    }
}