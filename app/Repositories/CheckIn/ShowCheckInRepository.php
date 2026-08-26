<?php

namespace App\Repositories\CheckIn;

use App\Repositories\BaseRepository;
use App\Models\{
    CheckIn,
};

class ShowCheckInRepository extends BaseRepository
{
    public function execute($checkIn){
        $gradeLevel = $checkIn->user->hasPersonalInfoValue(12) ? $checkIn->user->getPersonalInfoValue(12) : null;
        $yearLevel = $checkIn->user->hasPersonalInfoValue(13) ? $checkIn->user->getPersonalInfoValue(13) : null;
        $course = $checkIn->user->hasPersonalInfoValue(15) ? $checkIn->user->getPersonalInfoValue(15) : null;
        
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
