<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Http\Resources\ItemResource;
use App\Models\ActivityLog;

class UpdateItemRepository extends BaseRepository
{
    public function execute($request, $item)
    {
        $validated = $request->validated();
        try {
            $item->update($validated);

            ActivityLog::create([
                // 'group' => 'INVENTORY',
                'action' => "Item {$item->name} stock manually updated.",
                'performed_by' => auth()->id(),
            ]);

            return $this->success('Item updated successfully', new ItemResource($item), 200);
        } catch (\Exception $e) {
            return $this->error('Failed to update item', 500, $e->getMessage());
        }

    }
}
