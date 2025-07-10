<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Potato Chips',
                'Chocolate Bar',
                'Energy Drink',
                'Granola Bar',
                'Soda',
                'Crackers',
                'Candy Bar',
                'Trail Mix',
                'Cookies',
                'Water Bottle'
            ]),
            'description' => $this->faker->sentence(),
            'price_points' => $this->faker->numberBetween(2, 15),
            'product_category_id' => ProductCategory::factory(),
        ];
    }
}
