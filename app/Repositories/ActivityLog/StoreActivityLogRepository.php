<?php

namespace App\Repositories\ActivityLog;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;

class StoreActivityLogRepository extends BaseRepository
{
    public function execute($request){
        $log = ActivityLog::create([
            // 'group' => $request->input('group'),
            'action' => $request->input('action'),
            'performed_by' => auth()->id(),
        ]);

        return $this->success('Activity log created successfully.', $log, 200);
    }
}
