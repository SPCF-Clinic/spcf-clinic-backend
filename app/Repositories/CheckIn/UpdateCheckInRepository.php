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

class UpdateCheckInRepository extends BaseRepository
{
    public function execute($request, $checkIn){
        $validated = $request->validated();

        if ($validated['check_out']) {
            $checkIn->update([
                'check_out_time' => Carbon::now(),
                'status' => 'checked_out',
            ]);
            if ($checkIn->bed) {
                $checkIn->bed->update([
                    'status' => 'Empty',
                    'check_in_id' => null,
                ]);
                if (Carbon::now()->lt($checkIn->bed_check_out_time)) {
                    $checkIn->update([
                        'bed_check_out_time' => Carbon::now(),
                    ]);
                }
            }
        }

        if ($validated['unassign_bed']) {
            if ($checkIn->bed) {
                $checkIn->bed->update([
                    'status' => 'Empty',
                    'check_in_id' => null,
                ]);
                $checkIn->update([
                    'bed_check_out_time' => Carbon::now(),
                ]);
            }
        }

        if ($validated['bed_check_out_time']) {
            $checkIn->update([
                'bed_check_out_time' => $validated['bed_check_out_time'],
            ]);
        }

        if ($validated['dispensed_item_id']) {
            $item = Item::find($validated['dispensed_item_id']);
            if ($item) {
                DispensedItem::create([
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
                }
            }
        }

        $checkIn = new CheckInResource($checkIn);

        return $this->success('Check-in updated successfully.', $checkIn, 200);
    }
}
