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

class UpdateCheckInRepository extends BaseRepository
{
    public function execute($request, $checkIn){
        DB::beginTransaction();

        $validated = $request->validated();

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
                    ]);
                    if (Carbon::now()->lt($checkIn->bed_check_out_time)) {
                        $checkIn->update([
                            'bed_check_out_time' => Carbon::now(),
                        ]);
                    }
                }
            }

            if (isset($validated['unassign_bed'])) {
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

            if (isset($validated['bed_check_out_time'])) {
                $checkIn->update([
                    'bed_check_out_time' => Carbon::createFromFormat('H:i', $validated['bed_check_out_time']),
                ]);
            }

            if (isset($validated['dispensed_item_id'])) {
                $item = Item::find($validated['dispensed_item_id']);

                try {
                    if ($item) {
                        if ($item->quantity < ($validated['dispensed_item_quantity'] ?? 1)) {
                            return $this->error('Insufficient quantity available', 400);
                        }

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
