<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class DailyTransactionsChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Transactions (Last 7 Days)';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '500px';

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();

        $data = [];
        $labels = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $transactionCount = Transaction::whereDate('transaction_time', $date)
                ->count();

            $data[] = $transactionCount;
            $labels[] = $date->format('M j');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
