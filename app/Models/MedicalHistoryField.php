<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryField extends Model
{
    protected $fillable = [
        'is_default',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
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

    public function requiredByFields()
    {
        return $this->hasMany(MedicalHistoryFieldVersion::class, 'required_with_field_id');
    }

    public function userMedicalHistories()
    {
        return $this->hasMany(UserMedicalHistory::class, 'medical_history_field_id');
    }
}
