<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BedTimerPaused implements ShouldBroadcastNow
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
        return 'timer.paused';
    }

    public function broadcastWith(): array
    {
        return [
            'bed_id' => $this->bedId,
        ];
    }
}