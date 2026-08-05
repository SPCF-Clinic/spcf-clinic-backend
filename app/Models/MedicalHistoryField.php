<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryField extends Model
{
    public function versions()
    {
        return $this->hasMany(MedicalHistoryFieldVersion::class, 'medical_history_field_id');
    }

    public function latestVersion()
    {
        return $this->hasOne(MedicalHistoryFieldVersion::class, 'medical_history_field_id')->latestOfMany('version_number');
    }

    public function options()
    {
        return $this->hasManyThrough(
            MedicalHistoryFieldOption::class,
            MedicalHistoryFieldVersion::class,
            'medical_history_field_id',
            'field_version_id',
            'id',
            'id'
        );
    }
}
