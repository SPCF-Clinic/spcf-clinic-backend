<?php

namespace App\Traits;

use App\Models\{Department, Course};
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Shared logic for building dynamic validation rules from the live
 * PersonalInfoField / MedicalHistoryField definitions. Used by both the
 * registration form (everything required unless the field itself is
 * optional) and the student personal-info/medical-history update forms
 * (everything optional). The including class supplies presence rules via
 * fieldPresenceRule(), and — for updates — can fall back to a stored value
 * by overriding effectiveFieldValue().
 */
trait BuildsFieldAnswerRules
{
    /**
     * Every field of the given model that is actually collectible on the
     * form — i.e. not a non-answerable divider/section header.
     */
    protected function answerableFields(string $modelClass): Collection
    {
        return $modelClass::with('latestVersion.formFieldType', 'latestVersion.options')
            ->get()
            ->filter(fn ($field) => $field->latestVersion?->formFieldType?->is_answerable)
            ->values();
    }

    /**
     * Rejects any field id in the payload that doesn't correspond to a
     * currently answerable field (unknown, deleted, or a divider).
     */
    protected function knownFieldsRule(Collection $fields): \Closure
    {
        $knownIds = $fields->pluck('id')->all();

        return function ($attribute, $value, $fail) use ($knownIds) {
            if (! is_array($value)) {
                return;
            }

            $unknown = array_diff(array_keys($value), $knownIds);

            if (! empty($unknown)) {
                $fail('The '.$attribute.' contains unrecognized field(s): '.implode(', ', $unknown).'.');
            }
        };
    }

    /**
     * Build one validation rule set per field, keyed by "<group>.<field_id>"
     * so each field's presence/type rules apply to its own payload slot.
     */
    protected function fieldRules(Collection $fields, string $group): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $version = $field->latestVersion;
            $type = $version->formFieldType;
            $key = "{$group}.{$field->id}";

            $presence = (array) $this->fieldPresenceRule($version, $group);
            $optionValues = $type->has_options ? $version->options->pluck('option_value')->all() : [];

            if ($type->can_select_multiple) {
                $rules[$key] = array_merge($presence, ['array']);
                $rules["{$key}.*"] = ['string', Rule::in($optionValues)];

                continue;
            }

            $typeRules = match (true) {
                $type->has_options => [Rule::in($optionValues)],
                $type->name === 'Date' => ['date'],
                default => ['string', 'max:1000'],
            };

            $rules[$key] = array_merge($presence, $typeRules);

            // Preserve the business rule that a chosen Course must actually
            // belong to the chosen Department — the generic Rule::in(options)
            // check above only confirms the course *code* is valid
            // somewhere, not that it matches the department.
            if ($group === 'personal_info' && $version->field_name === 'Course') {
                $rules[$key][] = $this->courseBelongsToDepartmentRule($fields, $group);
            }
        }

        return $rules;
    }

    /**
     * Presence rule(s) for a given field — e.g. 'required', 'nullable', or
     * a required_if. Left to the including class since registration and
     * updates disagree on this.
     */
    abstract protected function fieldPresenceRule($version, string $group): string|array;

    /**
     * Course must belong to the *effective* Department — the submitted
     * value if present in this request, otherwise whatever
     * effectiveFieldValue() falls back to (nothing for registration; the
     * student's stored answer for updates).
     */
    protected function courseBelongsToDepartmentRule(Collection $personalInfoFields, string $group): \Closure
    {
        return function ($attribute, $value, $fail) use ($personalInfoFields, $group) {
            if ($value === null) {
                return;
            }

            $departmentField = $personalInfoFields->first(
                fn ($field) => $field->latestVersion?->field_name === 'Department'
            );

            $departmentCode = $departmentField
                ? $this->effectiveFieldValue($group, $departmentField->id)
                : null;

            $department = $departmentCode ? Department::where('code', $departmentCode)->first() : null;

            if (! $department || ! Course::where('department_id', $department->id)->where('code', $value)->exists()) {
                $fail('The selected course is invalid for the specified department.');
            }
        };
    }

    /**
     * The value to treat as "current" for a given field: the submitted
     * value if the key is present in this request, null otherwise.
     * Overridden by update requests to fall back to the stored answer
     * instead of null when the field was omitted.
     */
    protected function effectiveFieldValue(string $group, int $fieldId)
    {
        $groupInput = $this->input($group, []);

        return is_array($groupInput) ? ($groupInput[$fieldId] ?? null) : null;
    }
}