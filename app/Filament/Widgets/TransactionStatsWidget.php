<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's transactions
        $todayTransactions = Transaction::whereDate('transaction_time', $today)->count();
        $todayRevenue = Transaction::whereDate('transaction_time', $today)->sum('points_deducted');

        // This week's transactions
        $weekTransactions = Transaction::where('transaction_time', '>=', $thisWeek)->count();
        $weekRevenue = Transaction::where('transaction_time', '>=', $thisWeek)->sum('points_deducted');

        // This month's transactions
        $monthTransactions = Transaction::where('transaction_time', '>=', $thisMonth)->count();
        $monthRevenue = Transaction::where('transaction_time', '>=', $thisMonth)->sum('points_deducted');

        // All time
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::sum('points_deducted');

        return [
            Stat::make('Today\'s Transactions', $todayTransactions)
                ->description($todayRevenue . ' points earned')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('This Week', $weekTransactions)
                ->description($weekRevenue . ' points earned')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('This Month', $monthTransactions)
                ->description($monthRevenue . ' points earned')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('All Time', $totalTransactions)
                ->description($totalRevenue . ' points earned')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }
}
