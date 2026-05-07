<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use App\Models\InventoryMovement;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => InventoryMovement::TYPES[$state] ?? $state)
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('الكمية')
                    ->formatStateUsing(fn ($state, InventoryMovement $record): string => number_format($record->signed_quantity, 2))
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
                SelectFilter::make('type')
                    ->label('نوع الحركة')
                    ->options(InventoryMovement::TYPES),
                SelectFilter::make('reference_type')
                    ->label('نوع المرجع')
                    ->options(fn (): array => InventoryMovement::query()
                        ->whereNotNull('reference_type')
                        ->distinct()
                        ->orderBy('reference_type')
                        ->pluck('reference_type', 'reference_type')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
