<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id');
    }
}
