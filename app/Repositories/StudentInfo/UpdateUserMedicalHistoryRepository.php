<?php

namespace App\Repositories\StudentInfo;

use App\Repositories\BaseRepository;
use App\Repositories\Student\ShowStudentRepository;
use App\Models\User;
use App\Models\MedicalHistoryField;
use App\Models\ActivityLog;
use App\Support\FormVersion;

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
        $validated = $request->validated();

        if ($validated['form_version'] !== FormVersion::compute(MedicalHistoryField::class)) {
            return $this->error(
                'This form has changed since you loaded it. Please refresh the page and try again.',
                409
            );
        }

        $answers = $validated['medical_history'] ?? [];

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

        $fullName = $student->getFullNameAttribute();
        ActivityLog::create([
            // 'group' => 'STUDENT_RECORD',
            'action' => "{$fullName}'s information updated.",
            'performed_by' => auth()->id(),
        ]);

        return $this->success(
            'Medical history updated successfully.',
            $this->showStudent->buildPayload($student->fresh()),
            200
        );
    }
}