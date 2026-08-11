<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Http\Resources\CheckInResource;

class IndexStudentCheckInsRepository extends BaseRepository
{
    public function execute($request, $student){
        $perPage = $request->input('per_page', 20);
        $checkIns = $student->checkIns()->with(['bed', 'dispensedItems']);

        if ($request->has('search') && $request->search) {
            $checkIns->where(function ($query) use ($request) {
                $query->where('reason_for_visit', 'like', '%' . $request->search . '%')
                    ->orWhereHas('dispensedItems.item', function ($subquery) use ($request) {
                        $subquery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $checkIns = $checkIns->paginate($perPage);
        $paginationData = $this->pagePaginationData($checkIns);

        return $this->success('Student check-ins retrieved successfully.', [
            'check_ins' => CheckInResource::collection($checkIns),
            'pagination' => $paginationData
        ], 200);
    }
}
