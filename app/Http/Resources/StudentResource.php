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
        $firstName = $this->personalInfos->where('personal_info_field_id', 1)
            ->first()?->value;
        $lastName = $this->personalInfos->where('personal_info_field_id', 2)
            ->first()?->value;
        
        $gradeLevel = $this->personalInfos->where('personal_info_field_id', 12)
            ->first()?->value;
        $yearLevel = $this->personalInfos->where('personal_info_field_id', 13)
            ->first()?->value;
        $course = $this->personalInfos->where('personal_info_field_id', 15)
            ->first()?->value;

        $birthdate = $this->personalInfos->where('personal_info_field_id', 4)
            ->first()?->value;
        $age = $birthdate ? Carbon::parse($birthdate)->age : null;

        $sex = $this->personalInfos->where('personal_info_field_id', 5)
            ->first()?->value;

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $firstName . ' ' . $lastName,
            'grade_level' => $gradeLevel,
            'year_level' => $yearLevel,
            'course' => $course,
            'age' => $age,
            'sex' => $sex,
        ];
    }
}