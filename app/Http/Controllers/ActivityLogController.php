<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ActivityLog\{
    IndexActivityLogRequest,
    StoreActivityLogRequest,
};
use App\Repositories\ActivityLog\{
    IndexActivityLogRepository,
    StoreActivityLogRepository,
};
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    protected $index, $store;

    public function __construct(IndexActivityLogRepository $index, StoreActivityLogRepository $store)
    {
        $this->index = $index;
        $this->store = $store;
    }

    public function index(IndexActivityLogRequest $request)
    {
        $this->authorize('viewAny', ActivityLog::class);
        return $this->index->execute($request);
    }

    public function store(StoreActivityLogRequest $request)
    {
        $this->authorize('create', ActivityLog::class);
        return $this->store->execute($request);
    }
}
