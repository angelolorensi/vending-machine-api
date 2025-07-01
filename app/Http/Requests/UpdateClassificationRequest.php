<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|unique:classifications,name,' . $this->route('id') . ',classification_id',
            'daily_juice_limit' => 'sometimes|integer|min:0',
            'daily_meal_limit' => 'sometimes|integer|min:0',
            'daily_snack_limit' => 'sometimes|integer|min:0',
            'daily_point_limit' => 'sometimes|integer|min:0',
            'daily_point_recharge_amount' => 'sometimes|integer|min:0',
        ];
    }
}
