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
    UpdateCheckInRepository,
    ShowCheckInRepository
};

class CheckInController extends Controller
{
    protected $store, $index, $update, $show;

    public function __construct(
        StoreCheckInRepository $store,
        IndexCheckInRepository $index,
        UpdateCheckInRepository $update,
        ShowCheckInRepository $show
    ) {
        $this->store = $store;
        $this->index = $index;
        $this->update = $update;
        $this->show = $show;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', CheckIn::class);
        return $this->index->execute($request);
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

    public function show(CheckIn $checkIn)
    {
        $this->authorize('view', $checkIn);
        return $this->show->execute($checkIn);
    }
}
