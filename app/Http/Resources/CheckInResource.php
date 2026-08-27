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
        $gradeLevel = $this->user->hasPersonalInfoValue(12) ? $this->user->getPersonalInfoValue(12) : null;
        $yearLevel = $this->user->hasPersonalInfoValue(13) ? $this->user->getPersonalInfoValue(13) : null;
        $course = $this->user->hasPersonalInfoValue(15) ? $this->user->getPersonalInfoValue(15) : null;

        $birthDate = $this->user->hasPersonalInfoValue(4) ? $this->user->getPersonalInfoValue(4) : null;
        $age = $birthDate ? Carbon::parse($birthDate)->age : null;

        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->getFullNameAttribute(),
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
