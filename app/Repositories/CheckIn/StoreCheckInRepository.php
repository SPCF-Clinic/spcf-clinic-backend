<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\{
    CheckIn,
    Bed
};
use Carbon\Carbon;
use App\Http\Resources\CheckInResource;

class StoreCheckInRepository extends BaseRepository
{
    public function execute($request){
        $validated = $request->validated();

        if (CheckIn::where('user_id', $validated['user_id'])->where('status', 'Checked In')->exists()) {
            return $this->error('Student is already checked in.', 400);
        }

        if (isset($validated['bed_id'])) {
            $bed = Bed::find($validated['bed_id']);
            if (!$bed) {
                return $this->error('Bed not found.', 404);
            }
            if ($bed->status === 'Occupied') {
                return $this->error('Bed is already occupied.', 400);
            }
        }

        $checkIn = CheckIn::create([
            'user_id' => $validated['user_id'],
            'bed_id' => $validated['bed_id'] ?? null,
            'reason_for_visit' => $validated['reason_for_visit'],
            'check_in_time' => Carbon::now(),
            'check_out_time' => null,
            'bed_check_in_time' => Carbon::createFromFormat('H:i', $validated['bed_check_in_time']) ?? null,
            'bed_check_out_time' => Carbon::createFromFormat('H:i', $validated['bed_check_out_time']) ?? null,
            'status' => 'Checked In',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $bed = Bed::find($validated['bed_id'] ?? null);
        if ($bed) {
            $bed->update([
                'check_in_id' => $checkIn->id,
                'status' => 'Occupied',
            ]);
        }

        $checkIn = new CheckInResource($checkIn);

        return $this->success('Student checked in successfully.', $checkIn, 200);
    }
}
