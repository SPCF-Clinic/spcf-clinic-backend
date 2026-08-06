<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMedicalHistory extends Model
{
    protected $fillable = [
        'user_id',
        'medical_history_field_id',
        'value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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
