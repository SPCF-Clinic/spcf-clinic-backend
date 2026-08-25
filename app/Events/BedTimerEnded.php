<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A bed's timer reached its expiry — broadcast by the beds:check-timers
 * scheduled command, not by any user action. This exists purely to
 * resync a device whose own countdown drifted or was missed (e.g. the
 * tab was asleep): the frontend still owns the actual countdown/display
 * logic and should just snap this bed's timer to 0 on receipt, not
 * remove it from the list — the bed stays occupied until an admin
 * checks the student out or unassigns the bed.
 */
class BedTimerEnded implements ShouldBroadcastNow
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
        return 'timer.ended';
    }

    public function broadcastWith(): array
    {
        return [
            'bed_id' => $this->bedId,
        ];
    }
}