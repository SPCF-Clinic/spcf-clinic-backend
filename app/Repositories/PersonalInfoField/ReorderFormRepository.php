<?php

namespace App\Repositories\PersonalInfoField;

use App\Http\Resources\PersonalInfoFieldResource;
use App\Models\PersonalInfoField;
use App\Repositories\Support\AbstractReorderFormRepository;

class ReorderFormRepository extends AbstractReorderFormRepository
{
    protected function modelClass(): string
    {
        return PersonalInfoField::class;
    }

    protected function resourceClass(): string
    {
        return PersonalInfoFieldResource::class;
    }

    protected function requiredWithColumn(): string
    {
        return 'required_with_field_id';
    }

    protected function notFoundMessage(): string
    {
        return 'The selected personal info field could not be found.';
    }

    protected function defaultLockedMessage(): string
    {
        return 'Default personal info fields cannot be reordered.';
    }

    protected function notReorderableMessage(): string
    {
        return 'Personal info fields are not available for reordering.';
    }
}