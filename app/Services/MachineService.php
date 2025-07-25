<?php

namespace App\Services;

use App\Models\Machine;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Collection;

class MachineService
{
    public function getAllMachines(): Collection
    {
        return Machine::with('slots.product.productCategory')->get();
    }

    public function getMachineById(int $id): Machine
    {
        $machine = Machine::with('slots.product.productCategory')->find($id);

        if (!$machine) {
            throw new NotFoundException('Machine not found');
        }

        return $machine;
    }

    public function createMachine(array $data): Machine
    {
        $machine = Machine::create($data);

        for ($i = 1; $i <= 30; $i++) {
            $machine->slots()->create([
                'number' => $i,
                'product_id' => null,
            ]);
        }

        return $machine;
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
