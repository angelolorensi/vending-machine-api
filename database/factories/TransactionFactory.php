<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Card;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\Product;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'card_id' => Card::factory(),
            'machine_id' => Machine::factory(),
            'slot_id' => Slot::factory(),
            'product_id' => Product::factory(),
            'points_deducted' => $this->faker->numberBetween(2, 15),
            'transaction_time' => $this->faker->dateTimeThisMonth(),
            'status' => TransactionStatus::SUCCESS,
            'failure_reason' => null,
        ];
    }
}
