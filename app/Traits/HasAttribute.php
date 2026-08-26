<?php

namespace App\Traits;

trait HasAttribute
{
    public function getPersonalInfoValueByName($fieldName): ?string
    {
        return $this->personalInfos
            ->where(mb_strtolower('personal_info_field.latestVersion.field_name'), mb_strtolower($fieldName))
            ->first()?->value;
    }

    public function getPersonalInfoValue($fieldId): ?string
    {
        return $this->personalInfos
            ->where('personal_info_field_id', $fieldId)
            ->first()?->value;
    }

    public function hasPersonalInfoValue($fieldId = null, $fieldName = null): bool
    {
        if ($fieldName) {
            return $this->getPersonalInfoValueByName($fieldName) !== null;
        }
        if ($fieldId) {
            return $this->getPersonalInfoValue($fieldId) !== null;
        }
        return false;
    }
}
