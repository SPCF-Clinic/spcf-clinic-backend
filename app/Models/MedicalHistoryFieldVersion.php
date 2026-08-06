<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryFieldVersion extends Model
{
    protected $fillable = [
        'medical_history_field_id',
        'version_number',
        'field_name',
        'form_field_type_id',
        'is_required',
        'required_with_field_id',
        'required_with_field_value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function medicalHistoryField()
    {
        return $this->belongsTo(MedicalHistoryField::class, 'medical_history_field_id');
    }

    public function options()
    {
        return $this->hasMany(MedicalHistoryFieldOption::class, 'field_version_id');
    }

    public function formFieldType()
    {
        return $this->belongsTo(FormFieldType::class, 'form_field_type_id');
    }

    public function requiredWithField()
    {
        return $this->belongsTo(MedicalHistoryField::class, 'required_with_field_id');
    }
}
