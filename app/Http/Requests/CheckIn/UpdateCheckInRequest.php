<?php

namespace App\Http\Requests\CheckIn;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckInRequest extends FormRequest
{
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
        return [
            'check_out' => ['sometimes', 'nullable', 'boolean'],
            'unassign_bed' => ['sometimes', 'nullable', 'boolean'],
            'dispensed_item_id' => ['sometimes', 'nullable', 'exists:items,id'],
            'dispensed_item_quantity' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }
}
