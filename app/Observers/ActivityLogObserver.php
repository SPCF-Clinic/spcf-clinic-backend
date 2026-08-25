<?php

namespace App\Observers;

use App\Events\ActivityLogEvent;
use App\Models\ActivityLog;

class ActivityLogObserver
{
    /**
     * Fires for every ActivityLog::create() call anywhere in the app —
     * broadcasting from here means every call site gets this for free
     * instead of needing its own broadcast() call.
     */
    public function created(ActivityLog $activityLog): void
    {
        broadcast(ActivityLogEvent::fromModel($activityLog));
    }
}