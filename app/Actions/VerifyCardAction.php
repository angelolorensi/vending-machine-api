<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Exceptions\BlockedCardException;
use App\Exceptions\NotActiveException;
use App\Models\Card;
use App\Exceptions\NotFoundException;

class VerifyCardAction implements ActionContract
{
    public function execute(mixed ...$params): Card
    {
        [$cardNumber] = $params;

        $card = Card::with(['employee'])
            ->where('card_number', $cardNumber)
            ->first();

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        if ($card->status == CardStatus::BLOCKED) {
            throw new BlockedCardException();
        }

        if ($card->status !== CardStatus::ACTIVE) {
            throw new NotActiveException('Card is not active');
        }

        if (!$card->employee) {
            throw new \Exception('Card is not assigned to any employee');
        }

        if ($card->employee->status !== EmployeeStatus::ACTIVE) {
            throw new NotActiveException('Employee is not active');
        }

        return $card;
    }
}
