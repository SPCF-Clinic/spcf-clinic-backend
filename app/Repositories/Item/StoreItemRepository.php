<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\{
    Item,
    ItemContent
};
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ItemResource;

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
                $item->itemContent()->create([
                    'parent_type' => Item::class,
                    'parent_id' => $item->id,
                    'content_unit' => $request->item_content['content_unit'],
                    'quantity_per_item_unit' => $request->item_content['quantity_per_item_unit'],
                ]);
                
                if (isset($request->item_content['item_content'])) {
                    $item->itemContent->content()->create([
                        'parent_type' => ItemContent::class,
                        'parent_id' => $item->itemContent->id,
                        'content_unit' => $request->item_content['item_content']['content_unit'],
                        'quantity_per_item_unit' => $request->item_content['item_content']['quantity_per_item_unit'],
                    ]);
                }
            }

            DB::commit();
            return $this->success('Item created successfully', new ItemResource($item), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create item', 500, $e->getMessage());
        }
    }
}
