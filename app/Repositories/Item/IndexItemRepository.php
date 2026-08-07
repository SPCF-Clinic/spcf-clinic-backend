<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\{
    Item,
    MedicineContent
};
use App\Http\Resources\ItemResource;

class IndexItemRepository extends BaseRepository
{
    public function execute(){
        $items = Item::with('medicineContent')->get();
        return $this->success('Items retrieved successfully', ItemResource::collection($items), 200);
    }
}
