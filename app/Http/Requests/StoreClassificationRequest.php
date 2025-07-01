<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:classifications,name',
            'daily_juice_limit' => 'required|integer|min:0',
            'daily_meal_limit' => 'required|integer|min:0',
            'daily_snack_limit' => 'required|integer|min:0',
            'daily_point_limit' => 'required|integer|min:0',
        ];
    }
}
