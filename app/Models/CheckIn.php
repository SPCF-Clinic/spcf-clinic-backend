<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'bed_id',
        'reason_for_visit',
        'bed_check_in_time',
        'bed_check_out_time',
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

    public function dispensedItems()
    {
        return $this->hasMany(DispensedItem::class, 'check_in_id');
    }
}
