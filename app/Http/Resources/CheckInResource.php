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
        $gradeLevel = $this->user->personalInfos->where('personal_info_field_id', 12)
            ->first()?->value;
        $yearLevel = $this->user->personalInfos->where('personal_info_field_id', 13)
            ->first()?->value;
        $course = $this->user->personalInfos->where('personal_info_field_id', 15)
            ->first()?->value;

        $firstName = $this->user->personalInfos->where('personal_info_field_id', 1)
            ->first()?->value;
        $lastName = $this->user->personalInfos->where('personal_info_field_id', 2)
            ->first()?->value;

        $birthDate = $this->user->personalInfos->where('personal_info_field_id', 4)
            ->first()?->value;
        $age = $birthDate ? Carbon::parse($birthDate)->age : null;

        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $firstName . ' ' . $lastName,
                'age' => $age,
                'grade_level' => $gradeLevel,
                'year_level' => $yearLevel,
                'course' => $course,
            ] : null,
            'reason_for_visit' => $this->reason_for_visit,
            'check_in_time' => $this->check_in_time ? Carbon::parse($this->check_in_time)->format('Y-m-d H:i:s') : null,
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
