<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\CheckIn;
use App\Http\Resources\CheckInResource;

class IndexCheckInRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
            'page' => 'sometimes|nullable|integer|min:1',
            'search' => 'sometimes|nullable|string|max:255',
        ]);

        $checkIns = CheckIn::with(['user', 'bed']);

        if ($request->filled('search')) {
            $search = $request->search;

            $checkIns->whereHas('user', function ($q) use ($search) {
                    $q->fullNameLike($search);
                })->orWhere('reason_for_visit', 'like', "%{$search}%");
        }

        $checkIns = $checkIns->paginate($request->input('per_page', 20));

        $paginationData = $this->pagePaginationData($checkIns);
        $checkIns = CheckInResource::collection($checkIns);

        return $this->success('Check-ins retrieved successfully.', [
            'check_ins' => $checkIns,
            'pagination' => $paginationData
        ], 200);
    }
}
