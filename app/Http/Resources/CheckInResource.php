<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInResource extends JsonResource
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
            'user' => $this->user ? [
                'id' => $this->user->id,
                'student_id' => $this->user->username,
                'name' => $this->user->personalInfos ? 
                    $this->user->personalInfos->where('id', 1)->first()->field_value . ' ' . $this->user->personalInfos->where('id', 2)->first()->field_value : null,
            ] : null,
            'bed' => $this->bed ? [
                'id' => $this->bed->id,
                'bed_number' => $this->bed->bed_number,
                'status' => $this->bed->status,
                'bed_check_in_time' => $this->bed_check_in_time,
                'bed_check_out_time' => $this->bed_check_out_time,
            ] : null,
            'reason_for_visit' => $this->reason_for_visit,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ];
    }
}
