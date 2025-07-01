<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'card_id' => $this->card_id,
            'card_number' => $this->card_number,
            'points_balance' => $this->points_balance,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employee' => $this->whenLoaded('employee', function () {
                return [
                    'employee_id' => $this->employee->employee_id,
                    'name' => $this->employee->name,
                    'status' => $this->employee->status,
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
