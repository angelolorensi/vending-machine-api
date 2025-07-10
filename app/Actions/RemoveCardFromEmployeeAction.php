<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Services\EmployeeService;

class RemoveCardFromEmployeeAction implements ActionContract
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    public function execute(mixed ...$params): bool
    {
        [$employeeId] = $params;

        $employee = $this->employeeService->getEmployeeById($employeeId);

        if (!$employee->card_id) {
            throw new \Exception('Employee does not have a card assigned');
        }

        $this->employeeService->updateEmployee($employeeId, ['card_id' => null]);

        return true;
    }
}
