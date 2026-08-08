<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    CheckIn,
    User,
};

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            return $this->error('Unauthorized', 403);
        }
        $currentlyInClinicCount = CheckIn::where('status', 'Checked In')->count();
        $totalStudents = User::role('Student')->count();
        $totalVisits = CheckIn::count();

        $currentlyInClinic = CheckIn::with('user')
            ->where('status', 'Checked In')
            ->get()
            ->map(function ($checkIn) {
                $firstName = $checkIn->user->personalInfos->where('personal_info_field_id', 1)
                    ->first()?->value;
                $lastName = $checkIn->user->personalInfos->where('personal_info_field_id', 2)
                    ->first()?->value;

                return [
                    'id' => $checkIn->id,
                    'student_id' => $checkIn->user->username,
                    'name' => $firstName . ' ' . $lastName,
                    'bed_id' => $checkIn->bed->bed_number ?? null,
                    'check_in_time' => $checkIn->check_in_time,
                ];
            });

        return $this->success('Dashboard data retrieved successfully.', [
            'currently_in_clinic_count' => $currentlyInClinicCount,
            'total_students' => $totalStudents,
            'total_visits' => $totalVisits,
            'currently_in_clinic' => $currentlyInClinic,
        ], 200);
    }
}
