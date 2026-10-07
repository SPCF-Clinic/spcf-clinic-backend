<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {        
        $gradeLevel = $this->hasPersonalInfoValue(12) ? $this->getPersonalInfoValue(12) : null;
        $yearLevel = $this->hasPersonalInfoValue(13) ? $this->getPersonalInfoValue(13) : null;
        $course = $this->hasPersonalInfoValue(15) ? $this->getPersonalInfoValue(15) : null;

        $birthdate = $this->hasPersonalInfoValue(4) ? $this->getPersonalInfoValue(4) : null;
        $age = $birthdate ? Carbon::parse($birthdate)->age : null;

        $sex = $this->hasPersonalInfoValue(5) ? $this->getPersonalInfoValue(5) : null;

        $latestCheckIn = $this->latestCheckIn ? $this->latestCheckIn : null;
        $checkInStatus = $latestCheckIn ? $latestCheckIn->status : null;
        $currentBed = $latestCheckIn && $latestCheckIn->currentBed ? $latestCheckIn->currentBed : null;

        if ($latestCheckIn && $checkInStatus === 'Checked In') {
            if ($currentBed) {
                $status = $currentBed->bed_number;
            } else {
                $status = 'In Clinic';
            }
        } elseif ($latestCheckIn === null || $checkInStatus === 'Checked Out') {
            $status = 'Not In Clinic';
        }

        if ($this->trashed()) {
            $status = 'Archived';
        }

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->getFullNameAttribute(),
            'grade_level' => $gradeLevel,
            'year_level' => $yearLevel,
            'course' => $course,
            'age' => $age,
            'sex' => $sex,
            'status' => $status,
        ];
    }
}