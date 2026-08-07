<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\{
    Item,
    DispensedItem
};
use Illuminate\Support\Facades\DB;

class DispenseItemRepository extends BaseRepository
{
    public function execute($request){
        DB::beginTransaction();

        $validated = $request->validated();

        $item = Item::find($validated['item_id']);
        if (!$item) {
            return $this->error('Item not found', 404);
        }

        try {
            // Only automatically deduct the quantity for items with units "Tablets" or "Pairs"
            // Other units like "Boxes" or "Bottles" have to be updated manually when the whole pack is used up
            if (in_array($item->unit, ['Tablets', 'Pairs'])) {
                $item->update([
                    'quantity' => $item->quantity - $validated['quantity_dispensed']
                ]);
            }

            $dispensedItem = DispensedItem::create([
                'item_id' => $validated['item_id'],
                'quantity_dispensed' => $validated['quantity_dispensed'],
                'dispensed_to' => $validated['dispensed_to'],
                'dispensed_by' => auth()->id(),
            ]);

            DB::commit();

            $mappedDetails = [
                'item_name' => $dispensedItem->item->name,
                'quantity_dispensed' => $dispensedItem->quantity_dispensed,
                'dispensed_to' => $dispensedItem->dispensedTo->username,
                'dispensed_by' => $dispensedItem->dispensedBy->username,
            ];

            return $this->success('Item dispensed successfully', $mappedDetails, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to dispense item', 500, $e->getMessage());
        }
    }
}
