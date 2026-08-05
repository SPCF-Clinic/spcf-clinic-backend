<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\LaravelPermission\Traits\HasRoles;

#[Fillable([
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
    'course',
    'section',
    'mother_name',
    'father_name',
    'guardian_name',
    'guardian_contact_number',
    'emergency_contact_name',
    'emergency_contact_number',
    'covid_19_vaccination',
    'email',
    'password'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userMedicalHistory()
    {
        return $this->hasOne(UserMedicalHistory::class, 'user_id');
    }
}
