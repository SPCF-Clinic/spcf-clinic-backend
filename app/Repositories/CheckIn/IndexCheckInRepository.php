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
            'date_from' => 'sometimes|nullable|date',
            'date_to' => 'sometimes|nullable|date',
        ]);

        $checkIns = CheckIn::with(['user', 'bed']);

        if ($request->filled('search')) {
            $search = $request->search;

            $checkIns->whereHas('user', function ($q) use ($search) {
                    $q->fullNameLike($search);
                })->orWhere('reason_for_visit', 'like', "%{$search}%");
        }

        if ($request->filled('date_from')) {
            $checkIns->whereDate('check_in_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $checkIns->whereDate('check_in_time', '<=', $request->date_to);
        }

        // Newest first; id breaks ties so pages are stable.
        $checkIns = $checkIns
            ->orderByDesc('check_in_time')
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 20));

        $paginationData = $this->pagePaginationData($checkIns);
        $checkIns = CheckInResource::collection($checkIns);

        return $this->success('Check-ins retrieved successfully.', [
            'check_ins' => $checkIns,
            'pagination' => $paginationData
        ], 200);
    }
}
