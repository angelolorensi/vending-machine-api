<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_number' => 'required|string|max:255|unique:cards,card_number',
            'points_balance' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,blocked',
        ];
    }
}
