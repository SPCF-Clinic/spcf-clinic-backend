<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DispensedItemResource extends JsonResource
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
            'item' => [
                'id' => $this->item->id,
                'name' => $this->item->name,
                'unit' => $this->item->itemContent ? $this->item->itemContent->content_unit : $this->item->unit,
                'quantity' => $this->quantity_dispensed, 
            ],
            'dispensed_to' => [
                'id' => $this->dispensedTo?->id,
                'name' => $this->dispensedTo?->getFullNameAttribute(),
            ],
            'created_at' => Carbon::parse($this->created_at)->format('m-d-Y H:i:s'),
        ];
    }
}
