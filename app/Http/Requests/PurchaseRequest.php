<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_number' => 'required|string',
            'machine_id' => 'required|integer|exists:machines,machine_id',
            'slot_number' => 'required|integer|min:1|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'card_number.required' => 'Card number is required',
            'machine_id.exists' => 'Machine not found',
            'slot_number.min' => 'Slot number must be between 1 and 30',
            'slot_number.max' => 'Slot number must be between 1 and 30',
        ];
    }
}
