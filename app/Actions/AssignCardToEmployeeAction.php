<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Models\Card;
use App\Models\Employee;
use App\Exceptions\NotFoundException;

class AssignCardToEmployeeAction implements ActionContract
{
    public function execute(mixed ...$params): bool
    {
        [$cardId, $employeeId] = $params;

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
}
