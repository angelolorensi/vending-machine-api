<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DailyPointRechargeCommand extends Command
{
    protected $signature = 'points:daily-recharge';

    protected $description = 'Recharge daily points for all active employees on business days';

    public function handle()
    {
        if (!$this->isBusinessDay()) {
            $this->info('Today is not a business day. Skipping point recharge.');
            return 0;
        }

        $employees = Employee::with(['card', 'classification'])
            ->where('status', 'active')
            ->whereNotNull('card_id')
            ->whereHas('card', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        $rechargedCount = 0;
        $rechargedPoints = 0;

        foreach ($employees as $employee) {
            if ($employee->card && $employee->classification) {
                $rechargeAmount = $employee->classification->daily_point_recharge_amount;

                $employee->card->increment('points_balance', $rechargeAmount);

                $rechargedCount++;
                $rechargedPoints += $rechargeAmount;

                $this->info("Recharged {$rechargeAmount} points for employee: {$employee->name}");
            }
        }

        $this->info("Daily point recharge completed. {$rechargedCount} employees recharged.");
        $this->info("A total of {$rechargedPoints} points were recharged.");

        return 0;
    }

    private function isBusinessDay(): bool
    {
        $today = Carbon::now();
        return !in_array($today->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
    }
}
