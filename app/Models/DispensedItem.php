<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensedItem extends Model
{
    protected $fillable = [
        'item_id',
        'quantity_dispensed',
        'dispensed_to',
        'dispensed_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function dispensedTo()
    {
        return $this->belongsTo(User::class, 'dispensed_to');
    }
}
