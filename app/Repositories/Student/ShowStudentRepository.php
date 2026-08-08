<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    PersonalInfoField,
    MedicalHistoryField,
};
use App\Http\Resources\{
    StudentPersonalInfoFieldResource,
    StudentMedicalHistoryFieldResource,
};

class ShowStudentRepository extends BaseRepository
{
    public function execute(User $student)
    {
        if (! $student->hasRole('Student')) {
            return $this->error('Student not found.', 404);
        }

        return $this->success('Student retrieved successfully.', $this->buildPayload($student), 200);
    }

    /**
     * The full personal-info + medical-history view for a student: every
     * currently active field (always included), paired with the student's
     * answer where one exists. Reused by the personal-info/medical-history
     * update endpoints so they return the same consolidated shape after
     * writing changes.
     */
    public function buildPayload(User $student): array
    {
        $personalInfoFields = PersonalInfoField::with(
            'latestVersion.formFieldType',
            'latestVersion.options',
            'latestVersion.requiredWithField.latestVersion',
            'requiredByFields.formFieldType',
            'requiredByFields.options',
        )
            ->whereHas('latestVersion', fn ($query) => $query->whereNull('required_with_field_id'))
            ->whereHas('latestVersion.formFieldType', fn ($query) => $query->where('name', '!=', 'Divider'))
            ->get()
            ->sortBy($this->sortKey())
            ->values();

        $medicalHistoryFields = MedicalHistoryField::with(
            'latestVersion.formFieldType',
            'latestVersion.options',
            'latestVersion.requiredWithField.latestVersion',
            'requiredByFields.formFieldType',
            'requiredByFields.options',
        )
            ->whereHas('latestVersion', fn ($query) => $query->whereNull('required_with_field_id'))
            ->whereHas('latestVersion.formFieldType', fn ($query) => $query->where('name', '!=', 'Divider'))
            ->get()
            ->sortBy($this->sortKey())
            ->values();

        // Always reflect every currently active field, and only ever attach
        // an answer that still points to a live field — a null foreign key
        // means the field it was answered against has since been deleted,
        // and that stale answer is intentionally excluded here.
        $personalInfoAnswers = $student->personalInfos()
            ->whereNotNull('personal_info_field_id')
            ->get()
            ->keyBy('personal_info_field_id');

        $medicalHistoryAnswers = $student->medicalHistories()
            ->whereNotNull('medical_history_field_id')
            ->get()
            ->keyBy('medical_history_field_id');

        return [
            'id' => $student->id,
            'username' => $student->username,
            'personal_info' => StudentPersonalInfoFieldResource::collection($personalInfoFields, $personalInfoAnswers),
            'medical_history' => StudentMedicalHistoryFieldResource::collection($medicalHistoryFields, $medicalHistoryAnswers),
        ];
    }

    /**
     * Sort by numeric form_order (nulls last), tie-broken by id.
     */
    private function sortKey(): \Closure
    {
        return function ($field) {
            $order = $field->latestVersion?->form_order ?? PHP_INT_MAX;

            return str_pad((string) $order, 10, '0', STR_PAD_LEFT)
                . '-' . str_pad((string) $field->id, 10, '0', STR_PAD_LEFT);
        };
    }
}