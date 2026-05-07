<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('رمز الصنف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('اسم الصنف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('الوحدة'),
                TextColumn::make('current_quantity')
                    ->label('الكمية الحالية')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('minimum_quantity')
                    ->label('حد التنبيه')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('الحالة')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'نشط' : 'غير نشط'),
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
