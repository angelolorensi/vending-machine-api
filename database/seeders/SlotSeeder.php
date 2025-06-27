<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Slot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = Machine::all();

        foreach ($machines as $machine) {
            for ($slotNumber = 1; $slotNumber <= 30; $slotNumber++) {
                Slot::create([
                    'number' => $slotNumber,
                    'machine_id' => $machine->machine_id,
                    'product_id' => null,
                ]);
            }
        }
    }
}
