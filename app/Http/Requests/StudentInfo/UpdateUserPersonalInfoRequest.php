<?php

namespace App\Http\Requests\StudentInfo;

use App\Traits\BuildsFieldAnswerRules;
use App\Models\PersonalInfoField;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Partial update payload shape:
 *
 * { "personal_info": { "<personal_info_field_id>": "<value>", ... } }
 *
 * Every field is optional ('sometimes', 'nullable') — omit a field entirely
 * to leave its stored answer untouched, or submit it as null to clear it.
 * Type/option rules still apply to whatever is actually submitted.
 */
class UpdateUserPersonalInfoRequest extends FormRequest
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
        $fields = $this->answerableFields(PersonalInfoField::class);

        $rules = [
            'personal_info' => ['required', 'array', $this->knownFieldsRule($fields)],
        ];

        return array_merge($rules, $this->fieldRules($fields, 'personal_info'));
    }

    protected function fieldPresenceRule($version, string $group): array
    {
        return ['sometimes', 'nullable'];
    }

    /**
     * Falls back to the target student's currently stored answer when the
     * field wasn't included in this request, so cross-field checks (e.g.
     * Course/Department) validate against the effective value rather than
     * treating an omitted field as if it were cleared.
     */
    protected function effectiveFieldValue(string $group, int $fieldId)
    {
        $groupInput = $this->input($group, []);

        if (is_array($groupInput) && array_key_exists($fieldId, $groupInput)) {
            return $groupInput[$fieldId];
        }

        $student = $this->route('student');

        return $student
            ? $student->personalInfos()->where('personal_info_field_id', $fieldId)->value('value')
            : null;
    }
}