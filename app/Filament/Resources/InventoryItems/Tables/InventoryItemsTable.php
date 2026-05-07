<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use App\Models\InventoryItem;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->formatStateUsing(fn ($state, InventoryItem $record): string => $record->quantity_summary)
                    ->sortable(),
                TextColumn::make('minimum_quantity')
                    ->label('حد التنبيه')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'نشط' : 'غير نشط'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        true => 'نشط',
                        false => 'غير نشط',
                    ]),
                TernaryFilter::make('needs_restock')
                    ->label('يحتاج إعادة تزويد')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereColumn('current_quantity', '<=', 'minimum_quantity'),
                        false: fn (Builder $query): Builder => $query->whereColumn('current_quantity', '>', 'minimum_quantity'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
