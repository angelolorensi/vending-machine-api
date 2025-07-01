<?php

namespace App\Filament\Resources;

use App\Enums\EmployeeStatus;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $modelLabel = 'Funcionário';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('card_id')
                    ->relationship('card', 'card_number', function ($query, $record) {
                        return $query->where('status', 'active')
                            ->whereDoesntHave('employee', function ($subQuery) use ($record) {
                                if ($record) {
                                    $subQuery->where('employee_id', '!=', $record->employee_id);
                                }
                            })
                            ->orderBy('card_number');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('card_number')
                            ->required()
                            ->maxLength(255)
                            ->label('Número do Cartão'),
                        Forms\Components\TextInput::make('points_balance')
                            ->numeric()
                            ->default(0)
                            ->label('Saldo de Pontos'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                            ])
                            ->default('active')
                            ->label('Status'),
                    ])
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('classification_id')
                    ->relationship('classification', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(EmployeeStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('card.card_number')
                    ->label('Número do Cartão')
                    ->searchable(),
                Tables\Columns\TextColumn::make('classification.name')
                    ->label('Cargo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
