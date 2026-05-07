<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('inventoryItem.name')
                    ->label('الصنف')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('نوع الحركة')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('تكلفة الوحدة')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label('رقم المرجع')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
