<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalInfoFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestVersion = $this->latestVersion;

        $type = $latestVersion?->formFieldType ? [
            'name' => $latestVersion->formFieldType->name,
            'is_answerable' => (bool) $latestVersion->formFieldType->is_answerable,
            'has_options' => (bool) $latestVersion->formFieldType->has_options,
            'can_select_multiple' => (bool) $latestVersion->formFieldType->can_select_multiple,
        ] : null;

        $options = $latestVersion?->options?->map(function ($option) {
            return $option->option_value;
        })->values()->all();

        $requiredWithField = $latestVersion?->requiredWithField ? [
            'id' => $latestVersion->requiredWithField->id,
            'name' => $latestVersion->requiredWithField->latestVersion?->field_name ?? $latestVersion->requiredWithField->name,
        ] : null;

        $additionalFields = $this->requiredByFields ? $this->requiredByFields->groupBy('personal_info_field_id')->map(function ($fields) {
            $field = $fields->sortByDesc('version_number')
                ->first();

            $type = $field?->formFieldType ? [
                'name' => $field->formFieldType->name,
                'is_answerable' => (bool) $field->formFieldType->is_answerable,
                'has_options' => (bool) $field->formFieldType->has_options,
                'can_select_multiple' => (bool) $field->formFieldType->can_select_multiple,
            ] : null;

            return [
                'id' => $field->personal_info_field_id,
                'version_number' => $field->version_number,
                'name' => $field?->field_name,
                'type' => $type,
                'options' => $field?->options?->map(function ($option) {
                    return $option->option_value;
                })->values()->all() ?? [],
                'required_with_field_value' => $field?->required_with_field_value,
            ];
        })->values()->all() : [];

        return [
            'id' => $this->id,
            'name' => $latestVersion?->field_name ?? $this->name,
            'type' => $type,
            'options' => $options ?? [],
            'is_required' => (bool) ($latestVersion?->is_required ?? false),
            'required_with_field' => $requiredWithField,
            'additional_fields' => $additionalFields,
        ];
    }
}
