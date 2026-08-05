<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfoFieldVersion extends Model
{
    protected $fillable = [
        'personal_info_field_id',
        'version_number',
        'field_name',
        'field_type',
        'is_required',
    ];

    public function personalInfoField()
    {
        return $this->belongsTo(PersonalInfoField::class, 'personal_info_field_id');
    }

    public function options()
    {
        return $this->hasMany(PersonalInfoFieldOption::class, 'personal_info_field_version_id');
    }
}
