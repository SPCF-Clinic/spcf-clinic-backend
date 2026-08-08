<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;
use App\Models\Bed;

class IndexBedRepository extends BaseRepository
{
    public function execute(){
        $beds = Bed::with('currentCheckIn')->get()->map(function ($bed) {
            $firstName = $bed->currentCheckIn && $bed->currentCheckIn->user && $bed->currentCheckIn->user->personalInfos ? 
                $bed->currentCheckIn->user->personalInfos->where('personal_info_field_id', 1)->first()?->value : null;
            $lastName = $bed->currentCheckIn && $bed->currentCheckIn->user && $bed->currentCheckIn->user->personalInfos ? 
                $bed->currentCheckIn->user->personalInfos->where('personal_info_field_id', 2)->first()?->value : null;
            return [
                'id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'status' => $bed->status,
                'current_occupant' => $bed->currentCheckIn ? [
                    'user_id' => $bed->currentCheckIn->user_id,
                    'occupant_name' => $firstName . ' ' . $lastName,
                    'bed_check_in_time' => $bed->currentCheckIn->bed_check_in_time,
                    'bed_check_out_time' => $bed->currentCheckIn->bed_check_out_time
                ] : null,
            ];
        });
        return $this->success('Successfully retrieved beds.', $beds, 200);
    }
}
