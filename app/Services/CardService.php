<?php

namespace App\Services;

use App\Models\Card;
use App\Exceptions\NotFoundException;
use App\Models\Employee;
use Illuminate\Support\Str;

class CardService
{

    public function getCardById(int $id): Card
    {
        $card = Card::with(['employee', 'transactions'])->find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        return $card;
    }

    public function createCardForEmployee(int $employeeId): Card
    {
        // Check if employee already has a card
        $employee = Employee::find($employeeId);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        if ($employee->card_id) {
            throw new \Exception('Employee already has a card assigned');
        }

        $card = Card::create([
            'card_number' => strtoupper(Str::random(4)) . $employeeId,
            'points_balance' => 0,
            'status' => 'active',
        ]);

        $employee->update(['card_id' => $card->card_id]);

        return $card;
    }

    public function deleteCard(int $id): bool
    {
        $card = Card::find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        return $card->delete();
    }
}
