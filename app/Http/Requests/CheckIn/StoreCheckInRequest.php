<?php

namespace App\Http\Requests\CheckIn;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckInRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'bed_id' => ['sometimes', 'nullable', 'exists:beds,id'],
            'timer_expires_at' => ['required_with:bed_id', 'date', 'after:now'],
            'reason_for_visit' => ['required', 'string'],
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
