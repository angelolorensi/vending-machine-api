<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'classification_id' => 'sometimes|exists:classifications,classification_id',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
