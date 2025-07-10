<?php

namespace App\Services;

use App\Models\Slot;
use App\Exceptions\NotFoundException;

class SlotService
{
    public function getSlotById(int $id): Slot
    {
        $slot = Slot::with(['machine', 'product'])->find($id);

        if (!$slot) {
            throw new NotFoundException('Slot not found');
        }

        return $slot;
    }

    public function getSlotByMachineAndNumber(int $machineId, int $slotNumber): Slot
    {
        $slot = Slot::with('product')
            ->where('machine_id', $machineId)
            ->where('number', $slotNumber)
            ->first();

        if (!$slot) {
            throw new NotFoundException('Slot not found');
        }

        return $slot;
    }

    public function createSlot(array $data): Slot
    {
        return Slot::create($data);
    }

    public function updateSlot(int $id, array $data): Slot
    {
        $slot = Slot::find($id);

        if (!$slot) {
            throw new NotFoundException('Slot not found');
        }

        $slot->update($data);
        return $slot->fresh();
    }

    public function deleteSlot(int $id): bool
    {
        $slot = Slot::find($id);

        if (!$slot) {
            throw new NotFoundException('Slot not found');
        }

        return $slot->delete();
    }
}
