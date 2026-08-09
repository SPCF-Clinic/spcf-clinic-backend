<?php

namespace App\Repositories\ActivityLog;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;

class IndexActivityLogRepository extends BaseRepository
{
    public function execute($request){
        $query = ActivityLog::query();

        if ($request->has('group')) {
            $query->where('group', $request->input('group'));
        }

        if ($request->has('performed_by')) {
            $query->where('performed_by', $request->input('performed_by'));
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return $this->success('Activity logs retrieved successfully.', $logs, 200);
    }
}
