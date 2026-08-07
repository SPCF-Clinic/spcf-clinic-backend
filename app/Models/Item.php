<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public const CATEGORIES = [
        'Medicine' => ['Pain Reliever', 'Antibiotic', 'Cough & Cold', 'Antihistamine'],
        'Supply' => ['Wound Care', 'PPE']
    ];

    public const UNIT = [
        'Medicine' => ['Tablets', 'Boxes', 'Bottles'],
        'Supply' => ['Packs', 'Boxes', 'Pairs', 'Rolls']
    ];

    protected $fillable = [
        'name',
        'type',
        'category',
        'unit',
        'quantity',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
    
    public function itemContent()
    {
        return $this->hasOne(ItemContent::class, 'item_id');
    }
}
