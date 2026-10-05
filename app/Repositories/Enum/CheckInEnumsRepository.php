<?php

namespace App\Repositories\Enum;

use App\Repositories\BaseRepository;
use App\Models\CheckIn;
use App\Http\Resources\CheckInResource;

class CheckInEnumsRepository extends BaseRepository
{
    public function execute(){
        $checkIns = CheckIn::where('status', 'Checked In')
            ->where('current_bed_id', null)
            ->get();

        return $this->success('Successfully retrieved check-in enums.', CheckInResource::collection($checkIns), 200);
    }
}
