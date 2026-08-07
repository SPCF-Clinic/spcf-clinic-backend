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
        'Supply' => ['Packs', 'Boxes', 'Pair', 'Rolls']
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
    
    public function medicineContent()
    {
        return $this->hasOne(MedicineContent::class, 'item_id');
    }
}
