<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\DispensedItem;
use App\Http\Resources\DispensedItemResource;

class IndexDispensedItemRepository extends BaseRepository
{
    public function execute($request){
        $dispensedItems = DispensedItem::with(['item', 'dispensedTo'])->orderBy('created_at', 'desc')->get();

        return $this->success('Dispensed items retrieved successfully.', DispensedItemResource::collection($dispensedItems), 200);
    }
}
