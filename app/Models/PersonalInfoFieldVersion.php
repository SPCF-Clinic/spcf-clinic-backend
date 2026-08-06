<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalInfoFieldVersion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'personal_info_field_id',
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
        'deleted_at',
    ];

    public function personalInfoField()
    {
        return $this->belongsTo(PersonalInfoField::class, 'personal_info_field_id');
    }

    public function options()
    {
        return $this->hasMany(PersonalInfoFieldOption::class, 'field_version_id');
    }

    public function formFieldType()
    {
        return $this->belongsTo(FormFieldType::class, 'form_field_type_id');
    }

    public function requiredWithField()
    {
        return $this->belongsTo(PersonalInfoField::class, 'required_with_field_id');
    }
}
