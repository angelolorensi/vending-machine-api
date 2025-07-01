<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'classification_id' => $this->classification_id,
            'card_id' => $this->card_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'classification' => $this->whenLoaded('classification', function () {
                return [
                    'classification_id' => $this->classification->classification_id,
                    'name' => $this->classification->name,
                    'daily_juice_limit' => $this->classification->daily_juice_limit,
                    'daily_meal_limit' => $this->classification->daily_meal_limit,
                    'daily_snack_limit' => $this->classification->daily_snack_limit,
                    'daily_point_limit' => $this->classification->daily_point_limit,
                ];
            }),
            'card' => $this->whenLoaded('card', function () {
                return [
                    'card_id' => $this->card->card_id,
                    'card_number' => $this->card->card_number,
                    'points_balance' => $this->card->points_balance,
                    'status' => $this->card->status,
                ];
            }),
            'transactions' => $this->whenLoaded('transactions', function () {
                return $this->transactions->map(function ($transaction) {
                    return [
                        'transaction_id' => $transaction->transaction_id,
                        'points_deducted' => $transaction->points_deducted,
                        'transaction_time' => $transaction->transaction_time,
                        'status' => $transaction->status,
                    ];
                });
            }),
        ];
    }
}
