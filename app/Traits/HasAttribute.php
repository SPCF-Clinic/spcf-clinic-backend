<?php

namespace App\Traits;

trait HasAttribute
{
    public function getPersonalInfoValue($fieldId): ?string
    {
        return $this->personalInfos
            ->where('personal_info_field_id', $fieldId)
            ->first()?->value;
    }

    public function hasPersonalInfoValue($fieldId): bool
    {
        return $this->getPersonalInfoValue($fieldId) !== null;
    }
}
