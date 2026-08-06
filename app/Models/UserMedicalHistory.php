<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMedicalHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'medical_history_field_id',
        'value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicalHistoryField()
    {
        return $this->belongsTo(MedicalHistoryField::class, 'medical_history_field_id');
    }
}
