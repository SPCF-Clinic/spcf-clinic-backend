<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalInfoField extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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
}
