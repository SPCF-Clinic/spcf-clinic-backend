<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Http\Resources\ItemResource;

class UpdateItemRepository extends BaseRepository
{
    public function execute($request, $item)
    {
        $validated = $request->validated();
        try {
            $item->update($validated);
            return $this->success('Item updated successfully', new ItemResource($item), 200);
        } catch (\Exception $e) {
            return $this->error('Failed to update item', 500, $e->getMessage());
        }

    }
}
