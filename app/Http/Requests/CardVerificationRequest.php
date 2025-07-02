<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_number' => 'required|string'
        ];
    }
}
