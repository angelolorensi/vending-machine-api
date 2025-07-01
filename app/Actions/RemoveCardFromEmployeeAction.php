<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Models\Employee;
use App\Exceptions\NotFoundException;

class RemoveCardFromEmployeeAction implements ActionContract
{
    public function execute(mixed ...$params): bool
    {
        [$employeeId] = $params;

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
}
