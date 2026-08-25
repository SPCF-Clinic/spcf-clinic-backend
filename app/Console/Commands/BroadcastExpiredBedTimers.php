<?php

namespace App\Console\Commands;

use App\Events\BedTimerEnded;
use App\Models\Bed;
use Illuminate\Console\Command;

/**
 * Broadcasts a BedTimerEnded event for any bed whose timer_expires_at has
 * just passed. This does NOT drive the countdown itself — the frontend
 * owns that — it's purely a resync signal for a device that missed the
 * moment a timer hit zero (e.g. the tab was backgrounded), and it never
 * touches status/check_in_id, since the bed stays occupied until an admin
 * checks the student out or unassigns the bed.
 *
 * timer_ended_broadcast_at is stamped on broadcast so this stays
 * idempotent: without it, every run of this command (every second, per
 * the schedule below) would re-broadcast the same expired timer forever.
 * Starting or removing a timer resets it to null, so a new timer on the
 * same bed is tracked fresh.
 */
class BroadcastExpiredBedTimers extends Command
{
    protected $signature = 'beds:check-timers';

    protected $description = 'Broadcast a timer-ended event for any bed whose timer has just expired';

    public function handle(): void
    {
        Bed::whereNotNull('timer_expires_at')
            ->whereNull('timer_paused_at') // ignore paused timers, since they don't count down while paused
            ->where('timer_expires_at', '<=', now())
            ->whereNull('timer_ended_broadcast_at')
            ->each(function (Bed $bed) {
                $bed->update(['timer_ended_broadcast_at' => now()]);

                broadcast(new BedTimerEnded($bed->id));
            });
    }
}