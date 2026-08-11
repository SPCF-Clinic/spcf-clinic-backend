<?php

namespace App\Http\Requests\StudentInfo;

use App\Traits\BuildsFieldAnswerRules;
use App\Models\MedicalHistoryField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Partial update payload shape:
 *
 * {
 *   "form_version": "<from GET /medical-history-fields>",
 *   "medical_history": { "<medical_history_field_id>": "<value>", ... }
 * }
 *
 * Every field is optional ('sometimes', 'nullable') — omit a field entirely
 * to leave its stored answer untouched, or submit it as null to clear it.
 * Type/option rules still apply to whatever is actually submitted.
 *
 * form_version must match the current medical-history form fingerprint —
 * if an admin has since changed the field definitions, the request is
 * rejected so the client can prompt for a refresh instead of applying
 * answers against a form that's no longer accurate.
 */
class UpdateUserMedicalHistoryRequest extends FormRequest
{
    use BuildsFieldAnswerRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fields = $this->answerableFields(MedicalHistoryField::class);

        $rules = [
            'form_version' => 'required|string',
            'medical_history' => ['required', 'array', $this->knownFieldsRule($fields)],
        ];

        return array_merge($rules, $this->fieldRules($fields, 'medical_history'));
    }

    protected function fieldPresenceRule($version, string $group): array
    {
        return ['sometimes', 'nullable'];
    }

    /**
     * Falls back to the target student's currently stored answer when the
     * field wasn't included in this request.
     */
    protected function effectiveFieldValue(string $group, int $fieldId)
    {
        $groupInput = $this->input($group, []);

        if (is_array($groupInput) && array_key_exists($fieldId, $groupInput)) {
            return $groupInput[$fieldId];
        }

        $student = $this->route('student');

        return $student
            ? $student->medicalHistories()->where('medical_history_field_id', $fieldId)->value('value')
            : null;
    }
}