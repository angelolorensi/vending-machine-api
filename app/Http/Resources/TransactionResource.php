<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'employee_name' => $this->employee->name,
            'employee_number' => $this->employee->employee_number,
            'card_number' => $this->card->card_number,
            'machine_name' => $this->machine->name,
            'machine_location' => $this->machine->location,
            'slot_number' => $this->slot->number,
            'product_name' => $this->product->name,
            'product_category' => $this->product->productCategory->name,
            'category_color' => $this->product->productCategory->color,
            'points_deducted' => $this->points_deducted,
            'status' => $this->status,
            'failure_reason' => $this->failure_reason,
            'transaction_time' => $this->transaction_time->format('Y-m-d H:i:s'),
            'transaction_date' => $this->transaction_time->format('Y-m-d'),
            'transaction_time_formatted' => $this->transaction_time->format('M j, Y g:i A'),
        ];
    }
}