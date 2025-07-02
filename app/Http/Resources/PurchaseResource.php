<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product' => [
                'name' => $this->resource['product']['name'],
                'description' => $this->resource['product']['description'],
                'points_deducted' => $this->resource['product']['points_deducted'],
            ],
            'remaining_balance' => $this->resource['remaining_balance'],
            'transaction_id' => $this->resource['transaction_id'],
        ];
    }
}
