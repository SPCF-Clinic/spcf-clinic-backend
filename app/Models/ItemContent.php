<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemContent extends Model
{
    protected $fillable = [
        'item_id',
        'content_unit',
        'quantity_per_item_unit'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
