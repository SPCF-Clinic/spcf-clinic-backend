<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A bed's timer was explicitly removed — the bed was unassigned (or the
 * check-in was checked out, which also frees the bed) before its timer
 * expired. Frontends should remove this bed from their timers list
 * entirely, not show it at 0.
 */
class BedTimerRemoved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $bedId,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('beds')];
    }

    public function broadcastAs(): string
    {
        return 'timer.removed';
    }

    public function broadcastWith(): array
    {
        return [
            'bed_id' => $this->bedId,
        ];
    }
}