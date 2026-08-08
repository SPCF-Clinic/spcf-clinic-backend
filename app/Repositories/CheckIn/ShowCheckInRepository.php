<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\{
    CheckIn,
};

class ShowCheckInRepository extends BaseRepository
{
    public function execute($checkIn){
        $gradeLevel = $checkIn->user->personalInfos->where('personal_info_field_id', 12)
            ->first()?->value;
        $yearLevel = $checkIn->user->personalInfos->where('personal_info_field_id', 13)
            ->first()?->value;
        $course = $checkIn->user->personalInfos->where('personal_info_field_id', 15)
            ->first()?->value;
        
        $checkInData = [
            'id' => $checkIn->id,
            'name' => $checkIn->user->getStandardNameAttribute(),
            'grade_level' => $gradeLevel,
            'year_level' => $yearLevel,
            'course' => $course,
            'check_in_time' => $checkIn->check_in_time,
            'reason_for_visit' => $checkIn->reason_for_visit,
            'remarks' => $checkIn->remarks,
            'bed_id' => $checkIn->bed->bed_number ?? null,
            'items_dispensed' => $checkIn->dispensedItems->map(function ($dispensedItem) {
                return [
                    'item_name' => $dispensedItem->item->name,
                    'quantity_dispensed' => $dispensedItem->quantity_dispensed,
                    'unit' => $dispensedItem->item->itemContent ? $dispensedItem->item->itemContent->unit : $dispensedItem->item->unit,
                ];
            }),
        ];

        return $this->success('Check-in details retrieved successfully', $checkInData, 200);
    }
}
