<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dispensedItemsCount = $this->dispensedItems->where('created_at', '>=', now()->subDays(30))->sum('quantity_dispensed');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'unit' => $this->unit,
            'dispensed' => $dispensedItemsCount,
            'quantity' => $this->quantity,
            'item_content' => $this->when($this->itemContent, function () {
                return [
                    'content_unit' => $this->itemContent->content_unit,
                    'quantity_per_item_unit' => $this->itemContent->quantity_per_item_unit,
                ];
            }),
        ];
    }
}
