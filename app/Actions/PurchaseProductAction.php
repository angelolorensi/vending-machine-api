<?php

namespace App\Actions;

use App\Contracts\ActionContract;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Enums\MachineStatus;
use App\Enums\TransactionStatus;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotActiveException;
use App\Exceptions\InsufficientPointsException;
use App\Exceptions\DailyLimitExceededException;
use App\Models\Card;
use App\Models\Slot;
use App\Services\CardService;
use App\Services\MachineService;
use App\Services\SlotService;
use App\Services\TransactionService;
use Carbon\Carbon;

class PurchaseProductAction implements ActionContract
{
    public function __construct(
        private readonly CardService $cardService,
        private readonly MachineService $machineService,
        private readonly SlotService $slotService,
        private readonly TransactionService $transactionService
    ) {}

    public function execute(mixed ...$params): array
    {
        [$cardNumber, $machineId, $slotNumber] = $params;

        $card = $this->verifyCard($cardNumber);
        $machine = $this->machineService->getMachineById($machineId);
        $slot = $this->verifyMachineAndSlot($machineId, $slotNumber);

        if (!$slot->product) {
            throw new NotFoundException('No product in this slot');
        }

        if ($card->points_balance < $slot->product->price_points) {
            throw new InsufficientPointsException('Not enough points for this product');
        }

        $this->checkDailyLimits($card->employee, $slot->product);

        $transaction = $this->transactionService->createTransaction([
            'employee_id' => $card->employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $slot->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $slot->product->product_id,
            'points_deducted' => $slot->product->price_points,
            'transaction_time' => now(),
            'status' => TransactionStatus::COMPLETED,
            'failure_reason' => null,
        ]);

        $newBalance = $card->points_balance - $slot->product->price_points;
        $this->cardService->updateCard($card->card_id, [
            'points_balance' => $newBalance
        ]);

        return [
            'product' => [
                'name' => $slot->product->name,
                'description' => $slot->product->description,
                'points_deducted' => $slot->product->price_points,
            ],
            'remaining_balance' => $card->fresh()->points_balance,
            'transaction_id' => $transaction->transaction_id,
        ];
    }

    private function verifyCard(string $cardNumber): Card
    {
        $card = Card::with(['employee.classification'])
            ->where('card_number', $cardNumber)
            ->first();

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        if ($card->status !== CardStatus::ACTIVE) {
            throw new NotActiveException('Card is not active');
        }

        if (!$card->employee || $card->employee->status !== EmployeeStatus::ACTIVE) {
            throw new NotActiveException('Employee is not active');
        }

        return $card;
    }

    private function verifyMachineAndSlot(int $machineId, int $slotNumber): Slot
    {
        $machine = $this->machineService->getMachineById($machineId);

        if (!$machine) {
            throw new NotFoundException('Machine not found');
        }

        if ($machine->status !== MachineStatus::ACTIVE) {
            throw new NotActiveException('Machine is not active');
        }

        return $this->slotService->getSlotByMachineAndNumber($machineId, $slotNumber);
    }

    private function checkDailyLimits($employee, $product): void
    {
        $today = Carbon::today()->toDateString();

        $todayTransactions = $this->transactionService->getEmployeeDailyTransactions(
            $employee->employee_id,
            $today
        );

        $classification = $employee->classification;

        $categoryName = $product->productCategory->name;
        $categoryTransactions = $todayTransactions->filter(function ($transaction) use ($categoryName) {
            return $transaction->product->productCategory->name === $categoryName;
        })->count();

        switch (strtolower($categoryName)) {
            case 'beverages':
                if ($categoryTransactions >= $classification->daily_juice_limit) {
                    throw new DailyLimitExceededException('Daily juice limit exceeded');
                }
                break;
            case 'snacks':
                if ($categoryTransactions >= $classification->daily_snack_limit) {
                    throw new DailyLimitExceededException('Daily snack limit exceeded');
                }
                break;
            case 'meals':
                if ($categoryTransactions >= $classification->daily_meal_limit) {
                    throw new DailyLimitExceededException('Daily meal limit exceeded');
                }
                break;
        }

        $totalPointsUsed = $todayTransactions->sum('points_deducted');
        if (($totalPointsUsed + $product->price_points) > $classification->daily_point_limit) {
            throw new DailyLimitExceededException('Daily point limit would be exceeded');
        }
    }
}
