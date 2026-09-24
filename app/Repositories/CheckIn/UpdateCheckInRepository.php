<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\{
    CheckIn,
    DispensedItem,
    Item,
    Bed
};
use Carbon\Carbon;
use App\Http\Resources\CheckInResource;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Events\{
    BedTimerStarted,
    BedTimerRemoved,
    BedTimerPaused,
    BedTimerResumed,
    BedTimerAdjusted,
    CheckOutEvent,
};

class UpdateCheckInRepository extends BaseRepository
{
    public function execute($request, $checkIn){
        DB::beginTransaction();

        $validated = $request->validated();

        $fullName = $checkIn->user->getFullNameAttribute();
        $user = auth()->user();

        try {
            if (isset($validated['check_out']) && $validated['check_out']) {
                $checkIn->update([
                    'current_bed_id' => null,
                    'check_out_time' => Carbon::now(),
                    'status' => 'Checked Out',
                ]);
                if ($checkIn->bed) {
                    $checkIn->bed->update([
                        'status' => 'Empty',
                        'check_in_id' => null,
                        'timer_started_at' => null,
                        'timer_expires_at' => null,
                        'timer_ended_broadcast_at' => null,
                        'timer_paused_at' => null,
                    ]);

                    broadcast(new BedTimerRemoved($checkIn->bed->id));
                }

                ActivityLog::create([
                    'group' => 'CHECK-IN',
                    'action' => "{$fullName} checked out of the clinic.",
                    'performed_by' => auth()->id(),
                    'performed_for' => $checkIn->user_id,
                ]);

                broadcast(new CheckOutEvent($checkIn->id, $checkIn->user_id));
            }

            if (isset($validated['unassign_bed'])) {
                if ($checkIn->bed) {
                    $checkIn->update([
                        'current_bed_id' => null,
                    ]);

                    $checkIn->bed->update([
                        'status' => 'Empty',
                        'check_in_id' => null,
                        'timer_started_at' => null,
                        'timer_expires_at' => null,
                        'timer_ended_broadcast_at' => null,
                        'timer_paused_at' => null,
                    ]);

                    broadcast(new BedTimerRemoved($checkIn->bed->id));

                    ActivityLog::create([
                        'group' => 'BED',
                        'action' => "{$fullName} removed from {$checkIn->bed->bed_number}.",
                        'performed_by' => auth()->id(),
                        'performed_for' => $checkIn->user_id,
                    ]);
                }
            }

            if (isset($validated['bed_id'])) {
                $newBed = Bed::find($validated['bed_id']);
                if ($newBed) {
                    if ($checkIn->currentBed) {
                        return $this->error('User is already assigned to a bed. Please unassign the current bed first.', 400);
                    }
                    if ($newBed->status === 'Occupied') {
                        return $this->error('The selected bed is already occupied.', 400);
                    }

                    $newBed->update([
                        'status' => 'Occupied',
                        'check_in_id' => $checkIn->id,
                        'timer_started_at' => Carbon::now(),
                        'timer_expires_at' => $validated['timer_expires_at'],
                    ]);

                    $checkIn->update([
                        'current_bed_id' => $newBed->id,
                        'bed_id' => $newBed->id, // Update the historical bed assignment as well
                    ]);

                    broadcast(new BedTimerStarted($newBed->id, $newBed->timer_started_at, $newBed->timer_expires_at));

                    ActivityLog::create([
                        'group' => 'BED',
                        'action' => "{$fullName} assigned to {$newBed->bed_number}.",
                        'performed_by' => auth()->id(),
                        'performed_for' => $checkIn->user_id,
                    ]);
                }
            }

            if (isset($validated['timer_expires_at']) && !isset($validated['bed_id'])) {
                if ($checkIn->bed) {
                    $adjustedMinutes = Carbon::parse($checkIn->bed->timer_expires_at)->diffInMinutes($validated['timer_expires_at']);

                    $checkIn->bed->update([
                        'timer_expires_at' => $validated['timer_expires_at'],
                        'timer_ended_broadcast_at' => null,
                    ]);

                    ActivityLog::create([
                        'action' => "{$fullName}'s timer adjusted on {$checkIn->bed->bed_number} by {$adjustedMinutes} minute/s.",
                        'performed_by' => auth()->id(),
                        'performed_for' => $checkIn->user_id,
                    ]);

                    broadcast(new BedTimerAdjusted($checkIn->bed->id, $checkIn->bed->timer_expires_at));
                }
            }

            if (isset($validated['pause_timer'])) {
                if ($checkIn->bed) {
                    if ($checkIn->bed->timer_paused_at) {
                        return $this->error('Timer is already paused.', 400);
                    }
                    if (Carbon::now()->greaterThanOrEqualTo($checkIn->bed->timer_expires_at)) {
                        return $this->error('Cannot pause an expired timer.', 400);
                    }

                    $checkIn->bed->update([
                        'timer_paused_at' => Carbon::now(),
                    ]);

                    ActivityLog::create([
                        'action' => "{$fullName}'s timer paused on {$checkIn->bed->bed_number}.",
                        'performed_by' => auth()->id(),
                        'performed_for' => $checkIn->user_id,
                    ]);

                    broadcast(new BedTimerPaused($checkIn->bed->id));
                }
            }

            if (isset($validated['resume_timer'])) {
                if ($checkIn->bed) {
                    if (!$checkIn->bed->timer_paused_at) {
                        return $this->error('Timer is not paused.', 400);
                    }

                    $offsetTime = Carbon::parse($checkIn->bed->timer_paused_at)->diffInSeconds(Carbon::now(), true);

                    $checkIn->bed->update([
                        'timer_expires_at' => Carbon::parse($checkIn->bed->timer_expires_at)->addSeconds($offsetTime),
                    ]);

                    // Adjust timer_expires_at first so that the timer doesn't immediately expire after resuming

                    $checkIn->bed->update([
                        'timer_paused_at' => null,
                    ]);

                    ActivityLog::create([
                        'action' => "{$fullName}'s timer resumed on {$checkIn->bed->bed_number}.",
                        'performed_by' => auth()->id(),
                        'performed_for' => $checkIn->user_id,
                    ]);

                    broadcast(new BedTimerResumed($checkIn->bed->id, $checkIn->bed->timer_expires_at));
                }
            }

            if (isset($validated['dispensed_item_id'])) {
                $item = Item::find($validated['dispensed_item_id']);

                try {
                    if ($item) {
                        if ($item->itemContent && $item->itemContent->content) {
                            $quantity = $item->itemContent->content->quantity_per_item_unit * $item->itemContent->quantity_per_item_unit * $item->quantity;
                        } elseif ($item->itemContent && !$item->itemContent->content) {
                            $quantity = $item->itemContent->quantity_per_item_unit * $item->quantity;
                        } else {
                            $quantity = $item->quantity;
                        }

                        if ($quantity < ($validated['dispensed_item_quantity'] ?? 1)) {
                            return $this->error('Insufficient quantity available', 400);
                        }

                        $dispensedItem = DispensedItem::create([
                            'check_in_id' => $checkIn->id,
                            'item_id' => $item->id,
                            'quantity_dispensed' => $validated['dispensed_item_quantity'] ?? 1,
                            'dispensed_to' => $checkIn->user_id,
                            'dispensed_by' => auth()->id(),
                        ]);
                        if (in_array($item->unit, ['Tablets', 'Pairs'])) {
                            $item->update([
                                'quantity' => $item->quantity - ($validated['dispensed_item_quantity'] ?? 1),
                            ]);

                            ActivityLog::create([
                                'group' => 'INVENTORY',
                                'action' => "{$dispensedItem->quantity} {$item->unit} of {$item->name} dispensed to {$fullName}.",
                                'performed_by' => auth()->id(),
                                'performed_for' => $checkIn->user_id,
                                'item_id' => $item->id,
                            ]);
                        } else {
                            ActivityLog::create([
                                'group' => 'INVENTORY',
                                'action' => "{$dispensedItem->quantity} {$item->itemContent->content_unit} of {$item->name} dispensed to {$fullName}.",
                                'performed_by' => auth()->id(),
                                'performed_for' => $checkIn->user_id,
                                'item_id' => $item->id,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->error('Failed to dispense item', 500, $e->getMessage());
                }
            }

            $checkIn = new CheckInResource($checkIn);

            DB::commit();

            return $this->success('Check-in updated successfully.', $checkIn, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update check-in', 500, $e->getMessage());
        }
        
    }
}
