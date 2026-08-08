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
            'reason_for_visit' => ['required', 'string'],
            'bed_check_in_time' => ['required_with:bed_id', 'date'],
            'bed_check_out_time' => ['required_with:bed_id', 'date', 'after:bed_check_in_time'],
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
