<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => $this->faker->numberBetween(1, 30),
            'machine_id' => Machine::factory(),
            'product_id' => Product::factory(),
        ];
    }

    public function empty(): static
    {
        return $this->state([
            'product_id' => null,
        ]);
    }

    public function withProduct(Product $product): static
    {
        return $this->state([
            'product_id' => $product->product_id,
        ]);
    }
}
