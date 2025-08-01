<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionsByProductCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Transactions by Product Category';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '500px';

    protected function getData(): array
    {
        $transactions = Transaction::with('product.productCategory')
            ->get()
            ->groupBy('product.productCategory.name')
            ->map(fn ($group) => $group->count());

        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $transactions->values()->toArray(),
                    'backgroundColor' => [
                        '#ef4444', // red
                        '#f59e0b', // yellow
                        '#10b981', // green
                        '#3b82f6', // blue
                        '#8b5cf6', // purple
                        '#f97316', // orange
                    ],
                ],
            ],
            'labels' => $transactions->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
