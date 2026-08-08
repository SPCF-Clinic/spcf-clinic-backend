<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;
use App\Models\Bed;

class StoreBedRepository extends BaseRepository
{
    public function execute($request){
        $bed = Bed::create([
            'bed_number' => $request->bed_number,
        ]);

        return $this->success('Successfully created bed.', $bed, 200);
    }
}
