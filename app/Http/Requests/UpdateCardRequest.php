<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_number' => 'sometimes|string|max:255|unique:cards,card_number,' . $this->route('id') . ',card_id',
            'points_balance' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:active,inactive,blocked',
        ];
    }
}
