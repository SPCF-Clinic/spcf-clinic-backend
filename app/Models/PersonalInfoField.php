<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfoField extends Model
{
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
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
            'field_version_id',
            'id',
            'id'
        );
    }

    public function requiredByFields()
    {
        return $this->hasMany(PersonalInfoFieldVersion::class, 'required_with_field_id');
    }

    public function userPersonalInfos()
    {
        return $this->hasMany(UserPersonalInfo::class, 'personal_info_field_id');
    }
}
