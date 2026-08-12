<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;
use App\Models\Bed;
use App\Http\Resources\BedResource;

class IndexBedRepository extends BaseRepository
{
    public function execute(){
        $beds = Bed::with('currentCheckIn')
            ->orderBy('id', 'asc')
            ->cursorPaginate(20);
        
        return $this->success('Successfully retrieved beds.', $beds->through(fn($bed) => new BedResource($bed)), 200);
    }
}
