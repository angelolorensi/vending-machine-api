<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'description' => $this->description,
            'price_points' => $this->price_points,
            'product_category_id' => $this->product_category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => $this->whenLoaded('productCategory', function () {
                return [
                    'product_category_id' => $this->productCategory->product_category_id,
                    'name' => $this->productCategory->name,
                ];
            }),
            'slots' => $this->whenLoaded('slots', function () {
                return $this->slots->map(function ($slot) {
                    return [
                        'slot_id' => $slot->slot_id,
                        'number' => $slot->number,
                        'machine_id' => $slot->machine_id,
                    ];
                });
            }),
        ];
    }
}
