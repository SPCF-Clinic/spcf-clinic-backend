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
                    $itemContent = $item->itemContent()->create($validated['item_content']);
                    $item->setRelation('itemContent', $itemContent);
                }

                if (isset($validated['item_content']['item_content']) && $item->itemContent) {
                    if ($item->itemContent->content) {
                        $item->itemContent->content->update($validated['item_content']['item_content']);
                    } else {
                        $item->itemContent->content()->create($validated['item_content']['item_content']);
                    }
                } elseif (!isset($validated['item_content']['item_content']) && $item->itemContent && $item->itemContent->content) {
                    $item->itemContent->content()->delete();
                }
            } elseif (!isset($validated['item_content']) && $item->itemContent) {
                optional($item->itemContent->content)->delete(); 
                $item->itemContent()->delete();
                $item->unsetRelation('itemContent');
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

            $item->refresh()->load('itemContent.content');

            return $this->success('Item updated successfully', new ItemResource($item), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update item', 500, $e->getMessage());
        }

    }
}
