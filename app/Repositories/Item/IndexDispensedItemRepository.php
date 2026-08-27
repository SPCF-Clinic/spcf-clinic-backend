<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Models\DispensedItem;
use App\Http\Resources\DispensedItemResource;

class IndexDispensedItemRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'page' => 'sometimes|nullable|integer|min:1',
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
            'search' => 'sometimes|nullable|string|max:255',
        ]);

        $query = DispensedItem::with(['item', 'dispensedTo'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('item', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                ->orWhereIn('dispensed_to', function ($sub) use ($search) {
                    $sub->select('pi_first.user_id')
                        ->from('user_personal_infos as pi_first')
                        ->join('user_personal_infos as pi_last', function ($join) {
                            $join->on('pi_last.user_id', '=', 'pi_first.user_id')
                                ->where('pi_last.personal_info_field_id', 2); // last_name
                        })
                        ->leftJoin('user_personal_infos as pi_middle', function ($join) {
                            $join->on('pi_middle.user_id', '=', 'pi_first.user_id')
                                ->where('pi_middle.personal_info_field_id', 3); // middle_name
                        })
                        ->where('pi_first.personal_info_field_id', 1) // first_name
                        ->where(function ($q2) use ($search) {
                            $q2->whereRaw("CONCAT_WS(' ', pi_first.value, pi_last.value) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT_WS(' ', pi_last.value, pi_first.value) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT_WS(' ', pi_first.value, pi_middle.value, pi_last.value) LIKE ?", ["%{$search}%"]);
                        });
                });
            });
        }

        $dispensedItems = $query->paginate($request->input('per_page', 10));

        return $this->success('Dispensed items retrieved successfully.', [
            'dispensed_items' => DispensedItemResource::collection($dispensedItems),
            'pagination' => $this->pagePaginationData($dispensedItems),
        ], 200);
    }
}
