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
        return $this->index->execute();
    }

    public function store(StoreCheckInRequest $request)
    {
        return $this->store->execute($request);
    }

    public function update(UpdateCheckInRequest $request, CheckIn $checkIn)
    {
        return $this->update->execute($request, $checkIn);
    }
}
