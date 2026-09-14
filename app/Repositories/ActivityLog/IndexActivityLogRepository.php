<?php

namespace App\Repositories\ActivityLog;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;
use App\Http\Resources\ActivityLogResource;

class IndexActivityLogRepository extends BaseRepository
{
    public function execute($request){
        $perPage = $request->input('per_page', 20);
        $query = ActivityLog::query();

        $user = auth()->user();

        if (!$user->hasRole('Super Admin')) {
            $query->whereNotIn('group', ['INVENTORY']);
        }

        if ($request->has('performed_by') || $request->has('performed_for')) {
            $query->where('performed_by', $request->input('performed_by'))->orWhere('performed_for', $request->input('performed_for'));
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $logs = $query->orderBy('created_at', 'desc')->cursorPaginate($perPage);

        return $this->success('Activity logs retrieved successfully.', $logs->through(fn ($log) => new ActivityLogResource($log)), 200);
    }
}
