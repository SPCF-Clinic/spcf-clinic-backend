<?php

namespace App\Traits;

trait HasName
{
    public function getFirstNameAttribute(): ?string
    {
        return $this->personalInfos
            ->where('personal_info_field_id', 1)
            ->first()?->value;
    }

    public function getLastNameAttribute(): ?string
    {
        return $this->personalInfos
            ->where('personal_info_field_id', 2)
            ->first()?->value;
    }

    public function getMiddleNameAttribute(): ?string
    {
        return $this->personalInfos
            ->where('personal_info_field_id', 3)
            ->first()?->value;
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])->filter()->join(' ');
    }

    public function getStandardNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->last_name,
        ])->filter()->join(' ');
    }
}