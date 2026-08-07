<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Item\DispenseItemRepository;
use App\Http\Requests\Item\DispenseItemRequest;

class DispensedItemController extends Controller
{
    protected $dispense;

    public function __construct(
        DispenseItemRepository $dispense
    ) {
        $this->dispense = $dispense;
    }

    public function dispenseItem(DispenseItemRequest $request)
    {
        $this->authorize('create', DispensedItem::class);
        return $this->dispense->execute($request);
    }
}
