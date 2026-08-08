<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Http\Resources\CheckInResource;

class IndexStudentCheckInsRepository extends BaseRepository
{
    public function execute($request, $student){
        $checkIns = $student->checkIns()->with(['bed', 'dispensedItems']);

        if ($request->has('search') && $request->search) {
            $checkIns->where(function ($query) use ($request) {
                $query->where('reason_for_visit', 'like', '%' . $request->search . '%')
                    ->orWhereHas('dispensedItems.item', function ($subquery) use ($request) {
                        $subquery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        return $this->success('Student check-ins retrieved successfully.', CheckInResource::collection($checkIns->get()), 200);
    }
}
