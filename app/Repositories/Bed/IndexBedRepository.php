<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;
use App\Models\Bed;
use App\Http\Resources\BedResource;

class IndexBedRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'status' => ['sometimes', 'nullable', 'string', 'in:Occupied,Empty,All'],
        ]);

        if ($request->has('status') && $request->status !== 'All') {
            $beds = Bed::with('currentCheckIn')
                ->where('status', $request->status)
                ->orderBy('id', 'asc')
                ->cursorPaginate(20);
        } else {
            $beds = Bed::with('currentCheckIn')
                ->orderBy('id', 'asc')
                ->cursorPaginate(20);
        }

        return $this->success('Successfully retrieved beds.', $beds->through(fn($bed) => new BedResource($bed)), 200);
    }
}
