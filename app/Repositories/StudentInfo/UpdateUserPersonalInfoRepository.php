<?php

namespace App\Repositories\StudentInfo;

use App\Repositories\BaseRepository;
use App\Repositories\Student\ShowStudentRepository;
use App\Models\User;

class UpdateUserPersonalInfoRepository extends BaseRepository
{
    public function __construct(
        private ShowStudentRepository $showStudent,
    ) {}

    /**
     * Only the fields actually present in the request are touched: a
     * present field with a value is upserted, a present field with an
     * explicit null clears the stored answer, and any field omitted
     * entirely is left exactly as it was (the fallback to stored data the
     * request's own validation/cross-field checks already assume).
     */
    public function execute($request, User $student)
    {
        $answers = $request->validated()['personal_info'] ?? [];

        foreach ($answers as $fieldId => $value) {
            if ($value === null) {
                $student->personalInfos()->where('personal_info_field_id', $fieldId)->delete();

                continue;
            }

            $student->personalInfos()->updateOrCreate(
                ['personal_info_field_id' => $fieldId],
                ['value' => is_array($value) ? json_encode($value) : (string) $value],
            );
        }

        return $this->success(
            'Personal info updated successfully.',
            $this->showStudent->buildPayload($student->fresh()),
            200
        );
    }
}