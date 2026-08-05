<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryFieldVersion extends Model
{
    protected $fillable = [
        'medical_history_field_id',
        'version_number',
        'field_name',
        'field_type',
        'is_required',
    ];

    public function medicalHistoryField()
    {
        return $this->belongsTo(MedicalHistoryField::class, 'medical_history_field_id');
    }

    public function options()
    {
        return $this->hasMany(MedicalHistoryFieldOption::class, 'field_version_id');
    }
}
