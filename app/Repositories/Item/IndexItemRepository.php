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
    public function execute($request){
        $perPage = $request->input('per_page', 20);

        $items = Item::query()
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        $paginationData = $this->pagePaginationData($items);

        return $this->success('Items retrieved successfully', [
            'items' => ItemResource::collection($items),
            'pagination' => $paginationData
        ], 200);
    }
}
