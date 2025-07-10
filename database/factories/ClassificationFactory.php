<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'daily_juice_limit' => $this->faker->numberBetween(1, 5),
            'daily_meal_limit' => $this->faker->numberBetween(1, 3),
            'daily_snack_limit' => $this->faker->numberBetween(1, 5),
            'daily_point_limit' => $this->faker->numberBetween(10, 100),
            'daily_point_recharge_amount' => $this->faker->numberBetween(5, 50),
        ];
    }
}
