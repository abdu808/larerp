<?php

namespace App\Filament\Resources\FinancialAccounts\Tables;

use App\Models\FinancialAccount;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinancialAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('رمز الحساب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('اسم الحساب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FinancialAccount::ACCOUNT_TYPES[$state] ?? $state)
                    ->searchable(),
                TextColumn::make('current_balance')
                    ->label('الرصيد الحالي')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'نشط' : 'غير نشط'),
                TextColumn::make('created_at')
                    ->label('أضيف في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(FinancialAccount::ACCOUNT_TYPES),
                SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        true => 'نشط',
                        false => 'غير نشط',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
