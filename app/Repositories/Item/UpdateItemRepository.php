<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Http\Resources\ItemResource;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class UpdateItemRepository extends BaseRepository
{
    public function execute($request, $item)
    {
        DB::beginTransaction();

        $validated = $request->validated();

        $oldQuantity = $item->quantity;

        try {
            $item->update($validated);

            if (isset($validated['item_content'])) {
                if ($item->itemContent) {
                    $item->itemContent->update($validated['item_content']);
                } else {
                    $item->itemContent()->create($validated['item_content']);
                }

                if (isset($validated['item_content']['item_content'])) {
                    if ($item->itemContent->content) {
                        $item->itemContent->content->update($validated['item_content']['item_content']);
                    } else {
                        $item->itemContent->content()->create($validated['item_content']['item_content']);
                    }
                }
            }

            ActivityLog::create([
                'group' => 'INVENTORY',
                'action' => "Item {$item->name} updated.",
                'performed_by' => auth()->id(),
            ]);

            if ($oldQuantity != $item->quantity) {
                ActivityLog::create([
                    'group' => 'INVENTORY',
                    'action' => "Item {$item->name} stock manually updated.",
                    'performed_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return $this->success('Item updated successfully', new ItemResource($item), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update item', 500, $e->getMessage());
        }

    }
}
