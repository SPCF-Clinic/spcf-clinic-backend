<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'group',
        'action',
        'performed_by',
        'performed_for',
        'item_id',
    ];

    protected $hidden = [
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by')->withTrashed();
    }

    public function performedFor()
    {
        return $this->belongsTo(User::class, 'performed_for')->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
