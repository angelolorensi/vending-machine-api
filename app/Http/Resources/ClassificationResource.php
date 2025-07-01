<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'classification_id' => $this->classification_id,
            'name' => $this->name,
            'daily_juice_limit' => $this->daily_juice_limit,
            'daily_meal_limit' => $this->daily_meal_limit,
            'daily_snack_limit' => $this->daily_snack_limit,
            'daily_point_limit' => $this->daily_point_limit,
            'daily_point_recharge_amount' => $this->daily_point_recharge_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employees' => $this->whenLoaded('employees', function () {
                return $this->employees->map(function ($employee) {
                    return [
                        'employee_id' => $employee->employee_id,
                        'name' => $employee->name,
                        'status' => $employee->status,
                    ];
                });
            }),
        ];
    }
}
