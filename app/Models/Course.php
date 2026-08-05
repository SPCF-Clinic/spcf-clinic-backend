<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'name',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
