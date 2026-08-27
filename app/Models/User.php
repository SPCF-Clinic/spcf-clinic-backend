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
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasName;
use App\Traits\HasAttribute;
use App\Traits\SearchableByFullName;

#[Fillable([
    'username',
    'password'
])]
#[Hidden(['password', 'remember_token', 'created_at', 'updated_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, HasName, HasAttribute, SearchableByFullName;

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

    public function personalInfos()
    {
        return $this->hasMany(UserPersonalInfo::class, 'user_id');
    }

    public function medicalHistories()
    {
        return $this->hasMany(UserMedicalHistory::class, 'user_id');
    }

    public function dispensedItems()
    {
        return $this->hasMany(DispensedItem::class, 'dispensed_by');
    }

    public function dispensedItemsTo()
    {
        return $this->hasMany(DispensedItem::class, 'dispensed_to');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class, 'user_id');
    }

    public function latestCheckIn()
    {
        return $this->hasOne(CheckIn::class, 'user_id')->latestOfMany();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'performed_by');
    }
}