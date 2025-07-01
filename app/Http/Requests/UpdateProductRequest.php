<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price_points' => 'sometimes|integer|min:1',
            'product_category_id' => 'sometimes|exists:product_categories,product_category_id',
        ];
    }
}
