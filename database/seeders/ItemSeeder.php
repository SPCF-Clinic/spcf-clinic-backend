<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicineItems = [
            [
                'name' => 'Biogesic',
                'type' => 'Medicine',
                'category' => 'Pain Reliever',
                'unit' => 'Tablets',
                'quantity' => 100,
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'type' => 'Medicine',
                'category' => 'Antibiotic',
                'unit' => 'Bottles',
                'quantity' => 10,
                'item_content' => [
                    'content_unit' => 'ml',
                    'quantity_per_item_unit' => 100,
                ],
            ],
            [
                'name' => 'Robitussin',
                'type' => 'Medicine',
                'category' => 'Cough & Cold',
                'unit' => 'Bottles',
                'quantity' => 0,
                'item_content' => [
                    'content_unit' => 'ml',
                    'quantity_per_item_unit' => 120,
                ],
            ],
            [
                'name' => 'Cetirizine',
                'type' => 'Medicine',
                'category' => 'Antihistamine',
                'unit' => 'Boxes',
                'quantity' => 5,
                'item_content' => [
                    'content_unit' => 'Tablets',
                    'quantity_per_item_unit' => 100,
                ],
            ]
        ];

        $supplyItems = [
            [
                'name' => 'Cotton Balls',
                'type' => 'Supply',
                'category' => 'Wound Care',
                'unit' => 'Packs',
                'quantity' => 20,
            ],
            [
                'name' => 'Surgical Gloves',
                'type' => 'Supply',
                'category' => 'PPE',
                'unit' => 'Pairs',
                'quantity' => 15,
            ],
            [
                'name' => 'Gauze Bandage',
                'type' => 'Supply',
                'category' => 'Wound Care',
                'unit' => 'Rolls',
                'quantity' => 0,
            ],
            [
                'name' => 'Alcohol Swabs',
                'type' => 'Supply',
                'category' => 'Wound Care',
                'unit' => 'Boxes',
                'quantity' => 4,
                'item_content' => [
                    'content_unit' => 'pcs',
                    'quantity_per_item_unit' => 100,
                ],
            ]
        ];

        foreach ($medicineItems as $itemData) {
            $item = Item::create([
                'name' => $itemData['name'],
                'type' => $itemData['type'],
                'category' => $itemData['category'],
                'unit' => $itemData['unit'],
                'quantity' => $itemData['quantity'],
            ]);

            if (isset($itemData['item_content'])) {
                $item->itemContent()->create([
                    'content_unit' => $itemData['item_content']['content_unit'],
                    'quantity_per_item_unit' => $itemData['item_content']['quantity_per_item_unit'],
                ]);
            }
        }

        foreach ($supplyItems as $itemData) {
            $item = Item::create([
                'name' => $itemData['name'],
                'type' => $itemData['type'],
                'category' => $itemData['category'],
                'unit' => $itemData['unit'],
                'quantity' => $itemData['quantity'],
            ]);

            if (isset($itemData['item_content'])) {
                $item->itemContent()->create([
                    'content_unit' => $itemData['item_content']['content_unit'],
                    'quantity_per_item_unit' => $itemData['item_content']['quantity_per_item_unit'],
                ]);
            }
        }
    }
}
