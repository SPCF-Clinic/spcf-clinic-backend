<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\CheckIn;
use App\Http\Resources\CheckInResource;

class IndexCheckInRepository extends BaseRepository
{
    public function execute(){
        $checkIns = CheckIn::with(['user', 'bed'])
            ->get();

        $checkIns = CheckInResource::collection($checkIns);

        return $this->success('Check-ins retrieved successfully.', $checkIns, 200);
    }
}
