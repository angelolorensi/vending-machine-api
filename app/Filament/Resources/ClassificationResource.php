<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassificationResource\Pages;
use App\Filament\Resources\ClassificationResource\RelationManagers;
use App\Models\Classification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassificationResource extends Resource
{
    protected static ?string $model = Classification::class;

    protected static ?string $modelLabel = 'Cargo';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do cargo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('daily_juice_limit')
                    ->label('Limite diário de sucos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('daily_meal_limit')
                    ->label('Limite diário de refeições')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('daily_snack_limit')
                    ->label('Limite diário de lanches')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('daily_point_limit')
                    ->label('Limite diário de pontos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('daily_point_recharge_amount')
                    ->label('Quantidade de recarga diária de pontos')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do cargo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('daily_juice_limit')
                    ->label('Limite diário de sucos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_meal_limit')
                    ->label('Limite diário de refeições')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_snack_limit')
                    ->label('Limite diário de lanches')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_point_limit')
                    ->label('Limite diário de pontos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_point_recharge_amount')
                    ->label('Quantidade de recarga diária de pontos')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListClassifications::route('/'),
            'create' => Pages\CreateClassification::route('/create'),
            'edit' => Pages\EditClassification::route('/{record}/edit'),
        ];
    }
}
