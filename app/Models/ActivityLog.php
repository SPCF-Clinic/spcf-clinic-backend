<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'group',
        'action',
        'performed_by',
    ];

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
