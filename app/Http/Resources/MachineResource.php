<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'machine_id' => $this->machine_id,
            'name' => $this->name,
            'location' => $this->location,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'slots' => $this->whenLoaded('slots', function () {
                return $this->slots->map(function ($slot) {
                    return [
                        'slot_id' => $slot->slot_id,
                        'number' => $slot->number,
                        'quantity' => $slot->quantity,
                        'product' => $slot->product ? [
                            'product_id' => $slot->product->product_id,
                            'name' => $slot->product->name,
                            'description' => $slot->product->description,
                            'price_points' => $slot->product->price_points,
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
