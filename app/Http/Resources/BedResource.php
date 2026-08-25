<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            'timer_expires_at' => $this->timer_expires_at ? Carbon::parse($this->timer_expires_at)->format('Y-m-d H:i:s') : null,
            'current_occupant' => $this->currentCheckIn ? [
                'user_id' => $this->currentCheckIn->user_id,
                'occupant_name' => $this->currentCheckIn->user->getStandardNameAttribute(),
            ] : null,
        ];
    }
}
