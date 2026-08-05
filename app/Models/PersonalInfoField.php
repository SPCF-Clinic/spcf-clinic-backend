<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfoField extends Model
{
    public function latestVersion()
    {
        return $this->hasOne(PersonalInfoFieldVersion::class, 'personal_info_field_id')
            ->latestOfMany('version_number');
    }

    public function versions()
    {
        return $this->hasMany(PersonalInfoFieldVersion::class, 'personal_info_field_id');
    }

    public function options()
    {
        return $this->hasManyThrough(
            PersonalInfoFieldOption::class,
            PersonalInfoFieldVersion::class,
            'personal_info_field_id',
            'personal_info_field_version_id',
            'id',
            'id'
        );
    }
}
