<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryFieldOption extends Model
{
    protected $fillable = [
        'field_version_id',
        'option_value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function medicalHistoryFieldVersion()
    {
        return $this->belongsTo(MedicalHistoryFieldVersion::class, 'field_version_id');
    }

    public function medicalHistoryField() {
        return $this->belongsToThrough(MedicalHistoryField::class, MedicalHistoryFieldVersion::class, 'field_version_id', 'id', 'id', 'medical_history_field_id');
    }
}
