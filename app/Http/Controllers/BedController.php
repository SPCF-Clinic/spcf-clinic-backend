<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBedRequest;
use App\Repositories\Bed\{
    StoreBedRepository,
    IndexBedRepository,
    DeleteBedRepository
};
use App\Models\Bed;

class BedController extends Controller
{
    protected $index, $store, $delete;

    public function __construct(IndexBedRepository $index, StoreBedRepository $store, DeleteBedRepository $delete)
    {
        $this->index = $index;
        $this->store = $store;
        $this->delete = $delete;
    }

    public function index()
    {
        $this->authorize('viewAny', Bed::class);
        return $this->index->execute();
    }

    public function store(StoreBedRequest $request)
    {
        $this->authorize('create', Bed::class);
        return $this->store->execute($request);
    }

    public function destroy(Bed $bed)
    {
        $this->authorize('delete', $bed);
        return $this->delete->execute($bed);
    }
}
