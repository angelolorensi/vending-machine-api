<?php

namespace App\Services;

use App\Models\Machine;
use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Collection;

class MachineService
{

    public function getAllMachines(): Collection
    {
        return Machine::with('slots.product')->get();
    }

    public function getMachineById(int $id): Machine
    {
        $machine = Machine::with('slots.product')->find($id);

        if (!$machine) {
            throw new NotFoundException('Machine not found');
        }

        return $machine;
    }

    public function createMachine(array $data): Machine
    {
        return Machine::create($data);
    }

    public function updateMachine(int $id, array $data): Machine
    {
        $machine = Machine::find($id);

        if (!$machine) {
            throw new NotFoundException('Machine not found');
        }

        $machine->update($data);
        return $machine->fresh();
    }

    public function deleteMachine(int $id): bool
    {
        $machine = Machine::find($id);

        if (!$machine) {
            throw new NotFoundException('Machine not found');
        }

        return $machine->delete();
    }
}
