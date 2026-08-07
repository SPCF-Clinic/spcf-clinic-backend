<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\{
    Item,
    MedicineContent
};
use Illuminate\Support\Facades\DB;

class StoreItemRepository extends BaseRepository
{
    public function execute($request){
        DB::beginTransaction();
        try {
            $item = Item::create([
                'name' => $request->name,
                'type' => $request->type,
                'category' => $request->category,
                'unit' => $request->unit,
                'quantity' => $request->quantity,
            ]);

            if (in_array($request->unit, ['Boxes', 'Bottles'])) {
                $item->medicineContent()->create([
                    'content_unit' => $request->medicine_content['content_unit'],
                    'quantity_per_item_unit' => $request->medicine_content['quantity_per_item_unit'],
                ]);
            }

            DB::commit();
            return $this->success('Item created successfully', $item->load('medicineContent'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create item', 500, $e->getMessage());
        }
    }
}
