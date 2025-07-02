<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardVerificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'card_id' => $this->card_id,
            'card_number' => $this->card_number,
            'points_balance' => $this->points_balance,
            'employee_name' => $this->employee->name,
            'daily_point_limit' => $this->employee->classification->daily_point_limit,
        ];
    }
}
