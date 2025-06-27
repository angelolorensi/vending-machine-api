<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Snacks'],
            ['name' => 'Beverages'],
            ['name' => 'Candy'],
            ['name' => 'Healthy Options'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
