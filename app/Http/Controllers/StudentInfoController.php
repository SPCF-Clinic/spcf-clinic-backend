<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StudentInfo\{
    UpdateUserPersonalInfoRequest,
    UpdateUserMedicalHistoryRequest
};
use App\Repositories\StudentInfo\{
    UpdateUserPersonalInfoRepository,
    UpdateUserMedicalHistoryRepository
};

class StudentInfoController extends Controller
{
    protected $updatePersonalInfo, $updateMedicalHistory;

    public function __construct(
        UpdateUserPersonalInfoRepository $updatePersonalInfo,
        UpdateUserMedicalHistoryRepository $updateMedicalHistory
    ) {
        $this->updatePersonalInfo = $updatePersonalInfo;
        $this->updateMedicalHistory = $updateMedicalHistory;
    }

    public function updatePersonalInfo(UpdateUserPersonalInfoRequest $request, User $student)
    {
        $this->authorize('update', $student);
        return $this->updatePersonalInfo->execute($request, $student);
    }

    public function updateMedicalHistory(UpdateUserMedicalHistoryRequest $request, User $student)
    {
        $this->authorize('update', $student);
        return $this->updateMedicalHistory->execute($request, $student);
    }
}
