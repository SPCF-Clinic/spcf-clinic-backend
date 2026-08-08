<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CheckInResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstName = $this->user->personalInfos->where('personal_info_field_id', 1)
            ->first()?->value;
        $lastName = $this->user->personalInfos->where('personal_info_field_id', 2)
            ->first()?->value;

        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'student_id' => $this->user->username,
                'name' => $firstName . ' ' . $lastName,
            ] : null,
            'bed' => $this->bed ? [
                'id' => $this->bed->id,
                'bed_number' => $this->bed->bed_number,
                'status' => $this->bed->status,
                'bed_check_in_time' => $this->bed_check_in_time ? Carbon::parse($this->bed_check_in_time)->format('Y-m-d H:i:s') : null,
                'bed_check_out_time' => $this->bed_check_out_time ? Carbon::parse($this->bed_check_out_time)->format('Y-m-d H:i:s') : null,
            ] : null,
            'reason_for_visit' => $this->reason_for_visit,
            'check_in_time' => $this->check_in_time ? Carbon::parse($this->check_in_time)->format('Y-m-d H:i:s') : null,
            'check_out_time' => $this->check_out_time ? Carbon::parse($this->check_out_time)->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            'items_dispensed' => $this->dispensedItems->map(function ($item) {
                return [
                    'name' => $item->item->name,
                    'quantity' => $item->quantity_dispensed,
                ];
            }),
            'remarks' => $this->remarks,
        ];
    }
}
