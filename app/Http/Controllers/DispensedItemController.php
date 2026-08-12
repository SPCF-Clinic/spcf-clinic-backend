<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Item\{
    DispenseItemRepository,
    IndexDispensedItemRepository
};
use App\Http\Requests\Item\DispenseItemRequest;

class DispensedItemController extends Controller
{
    protected $dispense, $index;

    public function __construct(
        DispenseItemRepository $dispense,
        IndexDispensedItemRepository $index
    ) {
        $this->dispense = $dispense;
        $this->index = $index;
    }

    public function dispenseItem(DispenseItemRequest $request)
    {
        $this->authorize('create', DispensedItem::class);
        return $this->dispense->execute($request);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', DispensedItem::class);
        return $this->index->execute($request);
    }
}
