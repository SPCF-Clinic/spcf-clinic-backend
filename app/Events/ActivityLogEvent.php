<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogEvent implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $activityLogId,
        public string $action,
        public ?int $performedBy,
        public string $createdAt,
    ) {}

    public static function fromModel(ActivityLog $activityLog): self
    {
        return new self(
            $activityLog->id,
            $activityLog->action,
            $activityLog->performed_by,
            $activityLog->created_at,
        );
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'activity-log.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->activityLogId,
            'action' => $this->action,
            'performed_by' => $this->performedBy,
            'created_at' => $this->createdAt,
        ];
    }
}