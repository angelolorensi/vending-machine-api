<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Transactions';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::with(['employee', 'card', 'machine', 'product.productCategory'])
                    ->latest('transaction_time')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_time')
                    ->label('Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable(),

                Tables\Columns\TextColumn::make('card.card_number')
                    ->label('Card')
                    ->searchable(),

                Tables\Columns\TextColumn::make('machine.name')
                    ->label('Machine')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.productCategory.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn (Transaction $record): string => match ($record->product->productCategory->name) {
                        'Beverages' => 'danger',
                        'Snacks' => 'warning',
                        'Candy' => 'success',
                        'Healthy Options' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('points_deducted')
                    ->label('Points')
                    ->numeric()
                    ->suffix(' pts'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('transaction_time', 'desc');
    }
}