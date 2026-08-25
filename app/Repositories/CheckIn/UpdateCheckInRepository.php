<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\{
    CheckIn,
    DispensedItem,
    Item
};
use Carbon\Carbon;
use App\Http\Resources\CheckInResource;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Events\BedTimerRemoved;

class UpdateCheckInRepository extends BaseRepository
{
    public function execute($request, $checkIn){
        DB::beginTransaction();

        $validated = $request->validated();

        $fullName = $checkIn->user->getFullNameAttribute();
        $user = auth()->user();

        try {
            if (isset($validated['check_out'])) {
                $checkIn->update([
                    'check_out_time' => Carbon::now(),
                    'status' => 'Checked Out',
                ]);
                if ($checkIn->bed) {
                    $checkIn->bed->update([
                        'status' => 'Empty',
                        'check_in_id' => null,
                        'timer_expires_at' => null,
                        'timer_ended_broadcast_at' => null,
                    ]);

                    broadcast(new BedTimerRemoved($checkIn->bed->id));
                }

                ActivityLog::create([
                    // 'group' => 'CHECK-IN',
                    'action' => "{$fullName} checked out of the clinic.",
                    'performed_by' => auth()->id(),
                ]);
            }

            if (isset($validated['unassign_bed'])) {
                if ($checkIn->bed) {
                    $checkIn->bed->update([
                        'status' => 'Empty',
                        'check_in_id' => null,
                        'timer_expires_at' => null,
                        'timer_ended_broadcast_at' => null,
                    ]);

                    broadcast(new BedTimerRemoved($checkIn->bed->id));

                    ActivityLog::create([
                        // 'group' => 'BED',
                        'action' => "{$fullName} removed from {$checkIn->bed->bed_number}.",
                        'performed_by' => auth()->id(),
                    ]);
                }
            }

            if (isset($validated['dispensed_item_id'])) {
                $item = Item::find($validated['dispensed_item_id']);

                try {
                    if ($item) {
                        if ($item->quantity < ($validated['dispensed_item_quantity'] ?? 1)) {
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
                                // 'group' => 'INVENTORY',
                                'action' => "{$dispensedItem->quantity} {$item->unit} of {$item->name} dispensed to {$fullName}.",
                                'performed_by' => auth()->id(),
                            ]);
                        } else {
                            ActivityLog::create([
                                // 'group' => 'INVENTORY',
                                'action' => "{$dispensedItem->quantity} {$item->itemContent->unit} of {$item->name} dispensed to {$fullName}.",
                                'performed_by' => auth()->id(),
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
