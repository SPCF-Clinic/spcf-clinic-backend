<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Item\{
    StoreItemRequest,
};

use App\Repositories\Item\{
    StoreItemRepository,
};

use App\Models\Item;

class ItemController extends Controller
{
    protected $store;

    public function __construct(
        StoreItemRepository $store
    ) {
        $this->store = $store;
    }

    public function store(StoreItemRequest $request)
    {
        $this->authorize('create', Item::class);
        return $this->store->execute($request);
    }
}
