<?php

namespace App\Services;

use App\Models\Transaction;
use App\Exceptions\NotFoundException;
use App\Enums\TransactionStatus;
use Illuminate\Support\Collection;

class TransactionService
{
    public function getTransactionById(int $id): Transaction
    {
        $transaction = Transaction::with(['employee', 'card', 'machine', 'slot', 'product'])->find($id);

        if (!$transaction) {
            throw new NotFoundException('Transaction not found');
        }

        return $transaction;
    }

    public function createTransaction(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            throw new NotFoundException('Transaction not found');
        }

        $transaction->update($data);
        return $transaction->fresh();
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            throw new NotFoundException('Transaction not found');
        }

        return $transaction->delete();
    }

    public function getEmployeeDailyTransactions(int $employeeId, string $date): Collection
    {
        return Transaction::where('employee_id', $employeeId)
            ->whereDate('transaction_time', $date)
            ->where('status', TransactionStatus::COMPLETED)
            ->with('product.productCategory')
            ->get();
    }
}
