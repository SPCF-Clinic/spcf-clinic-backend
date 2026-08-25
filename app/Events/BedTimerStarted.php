<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A bed's timer was started (or restarted with a new expiry) — a student
 * was just assigned to it during check-in. Frontends listening on the
 * `beds` channel should add/update this bed in their timers list.
 *
 * Broadcasts synchronously (ShouldBroadcastNow, not the queued
 * ShouldBroadcast) — there's no queue worker expected to be running in
 * this deployment, and with only two devices to notify the extra latency
 * of an inline Pusher call is negligible.
 */
class BedTimerStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $bedId,
        public string $timerExpiresAt,
    ) {}

    /**
     * The channel this event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('beds')];
    }

    /**
     * The event name the frontend listens for.
     */
    public function broadcastAs(): string
    {
        return 'timer.started';
    }

    /**
     * Data sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'bed_id' => $this->bedId,
            'timer_expires_at' => $this->timerExpiresAt,
        ];
    }
}