<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineContent extends Model
{
    protected $fillable = [
        'item_id',
        'content_unit',
        'quantity_per_item_unit'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
