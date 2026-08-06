<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalInfoFieldOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'field_version_id',
        'option_value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function personalInfoFieldVersion()
    {
        return $this->belongsTo(PersonalInfoFieldVersion::class, 'field_version_id');
    }

    public function personalInfoField() {
        return $this->belongsToThrough(PersonalInfoField::class, PersonalInfoFieldVersion::class, 'field_version_id', 'id', 'id', 'personal_info_field_id');
    }
}
