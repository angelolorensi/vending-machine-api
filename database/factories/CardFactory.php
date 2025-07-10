<?php

namespace Database\Factories;

use App\Enums\CardStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'card_number' => strtoupper($this->faker->bothify('????####')),
            'points_balance' => $this->faker->numberBetween(0, 200),
            'status' => CardStatus::ACTIVE,
        ];
    }
}
