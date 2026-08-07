<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Item\{
    StoreItemRequest,
    UpdateItemRequest,
    IndexItemRequest
};

use App\Repositories\Item\{
    IndexItemRepository,
    StoreItemRepository,
    UpdateItemRepository,
    DeleteItemRepository
};

use App\Models\Item;

class ItemController extends Controller
{
    protected $index, $store, $update, $delete;

    public function __construct(
        StoreItemRepository $store,
        IndexItemRepository $index,
        UpdateItemRepository $update,
        DeleteItemRepository $delete
    ) {
        $this->store = $store;
        $this->index = $index;
        $this->update = $update;
        $this->delete = $delete;
    }

    public function index(IndexItemRequest $request)
    {
        $this->authorize('viewAny', Item::class);
        return $this->index->execute($request);
    }

    public function store(StoreItemRequest $request)
    {
        $this->authorize('create', Item::class);
        return $this->store->execute($request);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $this->authorize('update', $item);
        return $this->update->execute($request, $item);
    }

    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);
        return $this->delete->execute($item);
    }
}
