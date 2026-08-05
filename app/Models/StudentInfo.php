<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfo extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'last_name',
        'first_name',
        'middle_name',
        'birthdate',
        'gender',
        'religion',
        'nationality',
        'address',
        'contact_number',
        'education_level',
        'year_level',
        'course_id',
        'section',
        'mother_name',
        'father_name',
        'guardian_name',
        'guardian_contact_number',
        'emergency_contact_name',
        'emergency_contact_number',
        'covid_19_vaccination',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
