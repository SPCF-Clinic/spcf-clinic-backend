<?php

namespace App\Repositories\StudentInfo;

use App\Repositories\BaseRepository;
use App\Repositories\Student\ShowStudentRepository;
use App\Models\User;

class UpdateUserMedicalHistoryRepository extends BaseRepository
{
    public function __construct(
        private ShowStudentRepository $showStudent,
    ) {}

    /**
     * Only the fields actually present in the request are touched: a
     * present field with a value is upserted, a present field with an
     * explicit null clears the stored answer, and any field omitted
     * entirely is left exactly as it was.
     */
    public function execute($request, User $student)
    {
        $answers = $request->validated()['medical_history'] ?? [];

        foreach ($answers as $fieldId => $value) {
            if ($value === null) {
                $student->medicalHistories()->where('medical_history_field_id', $fieldId)->delete();

                continue;
            }

            $student->medicalHistories()->updateOrCreate(
                ['medical_history_field_id' => $fieldId],
                ['value' => is_array($value) ? json_encode($value) : (string) $value],
            );
        }

        return $this->success(
            'Medical history updated successfully.',
            $this->showStudent->buildPayload($student->fresh()),
            200
        );
    }
}