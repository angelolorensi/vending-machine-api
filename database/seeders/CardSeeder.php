<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = [
            ['card_number' => 'CARD001', 'points_balance' => 100, 'status' => 'active'],
            ['card_number' => 'CARD002', 'points_balance' => 75, 'status' => 'active'],
            ['card_number' => 'CARD003', 'points_balance' => 50, 'status' => 'active'],
            ['card_number' => 'CARD004', 'points_balance' => 25, 'status' => 'active'],
            ['card_number' => 'CARD005', 'points_balance' => 10, 'status' => 'active'],
            ['card_number' => 'CARD006', 'points_balance' => 0, 'status' => 'inactive'],
        ];

        foreach ($cards as $card) {
            Card::create($card);
        }
    }
}
