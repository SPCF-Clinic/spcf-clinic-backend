<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemContent extends Model
{
    protected $fillable = [
        'parent_type',
        'parent_id',
        'content_unit',
        'quantity_per_item_unit'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function parent()
    {
        return $this->morphTo();
    }

    public function content()
    {
        return $this->morphOne(ItemContent::class, 'parent');
    }
}
