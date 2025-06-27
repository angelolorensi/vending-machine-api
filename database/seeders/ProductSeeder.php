<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ProductCategory::whereIn('name', ['Snacks', 'Beverages', 'Candy', 'Healthy Options'])
            ->get()
            ->keyBy('name');

        $snacks = $categories['Snacks'];
        $beverages = $categories['Beverages'];
        $candy = $categories['Candy'];
        $healthy = $categories['Healthy Options'];

        $products = [
            // Snacks
            ['name' => 'Potato Chips', 'description' => 'Classic salted potato chips', 'price_points' => 5, 'product_category_id' => $snacks->getKey()],
            ['name' => 'Pretzels', 'description' => 'Crunchy salted pretzels', 'price_points' => 4, 'product_category_id' => $snacks->getKey()],
            ['name' => 'Crackers', 'description' => 'Cheese crackers', 'price_points' => 3, 'product_category_id' => $snacks->getKey()],

            // Beverages
            ['name' => 'Coca Cola', 'description' => 'Classic cola drink', 'price_points' => 6, 'product_category_id' => $beverages->getKey()],
            ['name' => 'Water', 'description' => 'Bottled water', 'price_points' => 2, 'product_category_id' => $beverages->getKey()],
            ['name' => 'Orange Juice', 'description' => 'Fresh orange juice', 'price_points' => 7, 'product_category_id' => $beverages->getKey()],

            // Candy
            ['name' => 'Chocolate Bar', 'description' => 'Milk chocolate bar', 'price_points' => 8, 'product_category_id' => $candy->getKey()],
            ['name' => 'Gummy Bears', 'description' => 'Fruit flavored gummy bears', 'price_points' => 4, 'product_category_id' => $candy->getKey()],

            // Healthy Options
            ['name' => 'Granola Bar', 'description' => 'Oats and honey granola bar', 'price_points' => 6, 'product_category_id' => $healthy->getKey()],
            ['name' => 'Trail Mix', 'description' => 'Nuts and dried fruit mix', 'price_points' => 7, 'product_category_id' => $healthy->getKey()],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
