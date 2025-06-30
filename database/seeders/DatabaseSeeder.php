<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClassificationSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            MachineSeeder::class,
            SlotSeeder::class,
            CardSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
