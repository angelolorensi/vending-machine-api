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

    public function createCard(array $data): Card
    {
        return Card::create($data);
    }

    public function updateCard(int $id, array $data): Card
    {
        $card = Card::find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        $card->update($data);
        return $card->fresh();
    }

    public function assignCardToEmployee(int $cardId, int $employeeId): bool
    {
        $employee = Employee::find($employeeId);
        $card = Card::find($cardId);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        if ($employee->card_id) {
            throw new \Exception('Employee already has a card assigned');
        }

        if ($card->employee) {
            throw new \Exception('Card is already assigned to another employee');
        }

        $employee->update(['card_id' => $card->card_id]);

        return true;
    }

    public function removeFromEmployee(int $employeeId): bool
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        if (!$employee->card_id) {
            throw new \Exception('Employee does not have a card assigned');
        }

        $employee->update(['card_id' => null]);

        return true;
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
