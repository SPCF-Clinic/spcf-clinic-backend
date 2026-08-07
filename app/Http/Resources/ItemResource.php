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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'unit' => $this->unit,
            'quantity' => $this->quantity,
            'medicine_content' => $this->when($this->medicineContent, function () {
                return [
                    'content_unit' => $this->medicineContent->content_unit,
                    'quantity_per_item_unit' => $this->medicineContent->quantity_per_item_unit,
                ];
            }),
        ];
    }
}
