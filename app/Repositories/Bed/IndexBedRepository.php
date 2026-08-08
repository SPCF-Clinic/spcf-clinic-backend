<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;
use App\Models\Bed;

class IndexBedRepository extends BaseRepository
{
    public function execute(){
        $beds = Bed::with('currentCheckIn')->map(function ($bed) {
            return [
                'bed_number' => $bed->bed_number,
                'status' => $bed->status,
                'current_occupant' => $bed->currentCheckIn ? [
                    'user_id' => $bed->currentCheckIn->user_id,
                    'occupant_name' => $bed->currentCheckIn->user->personalInfos ? 
                        $bed->currentCheckIn->user->personalInfos->where('id', 1)->first()->field_value . ' ' . $bed->currentCheckIn->user->personalInfos->where('id', 2)->first()->field_value : null,
                    'bed_check_in_time' => $bed->currentCheckIn->bed_check_in_time,
                    'bed_check_out_time' => $bed->currentCheckIn->bed_check_out_time
                ] : null,
            ];
        });
        return $this->success('Successfully retrieved beds.', $beds, 200);
    }
}
