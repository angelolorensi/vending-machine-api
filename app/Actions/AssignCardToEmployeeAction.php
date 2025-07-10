<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Services\CardService;
use App\Services\EmployeeService;

class AssignCardToEmployeeAction implements ActionContract
{
    public function __construct(
        private readonly CardService $cardService,
        private readonly EmployeeService $employeeService
    ) {}

    public function execute(mixed ...$params): bool
    {
        [$cardId, $employeeId] = $params;

        $employee = $this->employeeService->getEmployeeById($employeeId);
        $card = $this->cardService->getCardById($cardId);

        if ($employee->card_id) {
            throw new \Exception('Employee already has a card assigned');
        }

        if ($card->employee) {
            throw new \Exception('Card is already assigned to another employee');
        }

        $this->employeeService->updateEmployee($employeeId, ['card_id' => $card->card_id]);

        return true;
    }
}
