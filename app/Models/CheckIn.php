<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'bed_id', // for historical data viewing; never set to null
        'current_bed_id', // for current bed assignment; set to null when checked out or unassigned
        'reason_for_visit',
        'check_in_time',
        'check_out_time',
        'status',
        'remarks',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function currentBed()
    {
        return $this->belongsTo(Bed::class, 'current_bed_id');
    }

    public function dispensedItems()
    {
        return $this->hasMany(DispensedItem::class, 'check_in_id');
    }
}
