<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfoFieldOption extends Model
{
    protected $fillable = [
        'field_version_id',
        'option_value',
    ];

    public function personalInfoFieldVersion()
    {
        return $this->belongsTo(PersonalInfoFieldVersion::class, 'field_version_id');
    }

    public function personalInfoField() {
        return $this->belongsToThrough(PersonalInfoField::class, PersonalInfoFieldVersion::class, 'field_version_id', 'id', 'id', 'personal_info_field_id');
    }
}
