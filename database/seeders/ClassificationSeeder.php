<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = [
            [
                'name' => 'Manager',
                'daily_juice_limit' => 3,
                'daily_meal_limit' => 2,
                'daily_snack_limit' => 5,
                'daily_point_limit' => 50,
            ],
            [
                'name' => 'Senior Employee',
                'daily_juice_limit' => 2,
                'daily_meal_limit' => 1,
                'daily_snack_limit' => 3,
                'daily_point_limit' => 30,
            ],
            [
                'name' => 'Regular Employee',
                'daily_juice_limit' => 1,
                'daily_meal_limit' => 1,
                'daily_snack_limit' => 2,
                'daily_point_limit' => 20,
            ],
            [
                'name' => 'Intern',
                'daily_juice_limit' => 1,
                'daily_meal_limit' => 0,
                'daily_snack_limit' => 1,
                'daily_point_limit' => 10,
            ],
        ];

        foreach ($classifications as $classification) {
            Classification::create($classification);
        }
    }
}
