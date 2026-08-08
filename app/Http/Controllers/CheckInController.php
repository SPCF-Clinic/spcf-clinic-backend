<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckIn;
use App\Http\Requests\CheckIn\{
    StoreCheckInRequest,
    UpdateCheckInRequest
};
use App\Repositories\CheckIn\{
    StoreCheckInRepository,
    IndexCheckInRepository,
    UpdateCheckInRepository
};

class CheckInController extends Controller
{
    protected $store, $index, $update;

    public function __construct(
        StoreCheckInRepository $store,
        IndexCheckInRepository $index,
        UpdateCheckInRepository $update
    ) {
        $this->store = $store;
        $this->index = $index;
        $this->update = $update;
    }

    public function index()
    {
        $this->authorize('viewAny', CheckIn::class);
        return $this->index->execute();
    }

    public function store(StoreCheckInRequest $request)
    {
        $this->authorize('create', CheckIn::class);
        return $this->store->execute($request);
    }

    public function update(UpdateCheckInRequest $request, CheckIn $checkIn)
    {
        $this->authorize('update', $checkIn);
        return $this->update->execute($request, $checkIn);
    }
}
