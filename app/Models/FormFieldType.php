<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormFieldType extends Model
{
    protected $fillable = [
        'name',
        'is_answerable',
        'has_options',
        'can_select_multiple',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function personalInfoFieldVersions()
    {
        return $this->hasMany(PersonalInfoFieldVersion::class, 'form_field_type_id');
    }

    public function medicalHistoryFieldVersions()
    {
        return $this->hasMany(MedicalHistoryFieldVersion::class, 'form_field_type_id');
    }
}
