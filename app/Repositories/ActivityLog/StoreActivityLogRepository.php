<?php

namespace App\Repositories\ActivityLog;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;
use App\Models\User;

class StoreActivityLogRepository extends BaseRepository
{
    public function execute($request){
        $user = User::withTrashed()->find($request->input('performed_for'));
        if ($user && $user->trashed()) {
            return $this->error('Cannot create activity log for an archived user', 400);
        }
        
        $log = ActivityLog::create([
            'group' => $request->input('group'),
            'action' => $request->input('action'),
            'performed_by' => auth()->id(),
            'performed_for' => $request->input('performed_for'),
        ]);

        return $this->success('Activity log created successfully.', $log, 200);
    }
}
