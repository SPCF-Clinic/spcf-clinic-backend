<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $fillable = [
        'bed_number',
        'check_in_id',
        'status',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function currentCheckIn()
    {
        return $this->belongsTo(CheckIn::class, 'check_in_id');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class, 'bed_id');
    }
}
