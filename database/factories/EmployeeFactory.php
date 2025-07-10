<?php

namespace Database\Factories;

use App\Enums\EmployeeStatus;
use App\Models\Classification;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'classification_id' => Classification::factory(),
            'card_id' => null,
            'status' => EmployeeStatus::ACTIVE,
        ];
    }
}
