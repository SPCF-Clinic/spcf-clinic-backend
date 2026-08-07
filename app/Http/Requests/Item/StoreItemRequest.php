<?php

namespace App\Http\Requests\Item;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Item;

class StoreItemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Medicine,Supply'],
            'category' => ['required', Rule::in(Item::CATEGORIES[$this->input('type')])],
            'unit' => ['required', Rule::in(Item::UNIT[$this->input('type')])],
            'quantity' => ['required', 'integer', 'min:0'],
            'medicine_content.content_unit' => ['required_if:unit,Boxes', 'required_if:unit,Bottles', 'in:ml,Tablets'],
            'medicine_content.quantity_per_item_unit' => ['required_if:unit,Boxes', 'required_if:unit,Bottles', 'integer', 'min:1'],
        ];
    }
}
