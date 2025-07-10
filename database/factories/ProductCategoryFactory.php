<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Snacks',
                'Beverages',
                'Candy',
                'Healthy Options',
                'Meals',
                'Desserts'
            ]),
        ];
    }
}
