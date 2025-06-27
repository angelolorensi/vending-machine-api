<?php

namespace Database\Seeders;

use App\Enums\MachineStatus;
use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [
            ['name' => 'Machine 01', 'location' => 'Main Lobby', 'status' => MachineStatus::ACTIVE],
            ['name' => 'Machine 02', 'location' => 'Floor 2 Break Room', 'status' => MachineStatus::ACTIVE],
            ['name' => 'Machine 03', 'location' => 'Cafeteria', 'status' => MachineStatus::ACTIVE],
            ['name' => 'Machine 04', 'location' => 'Floor 3 Office Area', 'status' => MachineStatus::ACTIVE],
            ['name' => 'Machine 05', 'location' => 'Parking Garage', 'status' => MachineStatus::INACTIVE],
        ];

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}
