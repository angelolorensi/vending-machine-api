<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TransactionStatsWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\TransactionsByProductCategoryChart;
use App\Filament\Widgets\DailyTransactionsChart;
use Filament\Pages\Page;

class TransactionDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.transaction-dashboard';

    protected static ?string $title = 'Transaction Dashboard';

    protected static ?string $navigationLabel = 'Transaction Dashboard';

    protected static ?int $navigationSort = 1;

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionStatsWidget::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            RecentTransactionsWidget::class,
            TransactionsByProductCategoryChart::class,
            DailyTransactionsChart::class,
        ];
    }
}
