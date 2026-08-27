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
                    ->orWhereHas('dispensedTo', fn ($q2) => $q2->fullNameLike($search));
            });
        }

        $dispensedItems = $query->paginate($request->input('per_page', 10));

        return $this->success('Dispensed items retrieved successfully.', [
            'dispensed_items' => DispensedItemResource::collection($dispensedItems),
            'pagination' => $this->pagePaginationData($dispensedItems),
        ], 200);
    }
}
