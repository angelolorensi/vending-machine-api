<?php

namespace Database\Factories;

use App\Enums\MachineStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Machine ' . $this->faker->numberBetween(1, 100),
            'location' => $this->faker->randomElement([
                'Main Lobby',
                'Floor 2 Break Room',
                'Cafeteria',
                'Office Area',
                'Parking Garage',
                'Reception',
                'Conference Room'
            ]),
            'status' => MachineStatus::ACTIVE,
        ];
    }
}
