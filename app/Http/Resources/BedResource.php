<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedResource extends JsonResource
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
            'bed_number' => $this->bed_number,
            'status' => $this->status,
            'current_occupant' => $this->currentCheckIn ? [
                'user_id' => $this->currentCheckIn->user_id,
                'occupant_name' => $this->currentCheckIn->user->getStandardNameAttribute(),
            ] : null,
        ];
    }
}
