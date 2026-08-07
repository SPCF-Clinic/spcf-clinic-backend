<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\{
    Item,
    ItemContent
};
use App\Http\Resources\ItemResource;

class IndexItemRepository extends BaseRepository
{
    public function execute(){
        $items = Item::with('itemContent')->get();
        return $this->success('Items retrieved successfully', ItemResource::collection($items), 200);
    }
}
