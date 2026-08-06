<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPersonalInfo extends Model
{
    protected $fillable = [
        'user_id',
        'personal_info_field_id',
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

    public function personalInfoField()
    {
        return $this->belongsTo(PersonalInfoField::class, 'personal_info_field_id');
    }
}
