<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('financialAccount.name')
                    ->label('الحساب')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->searchable(),
                TextColumn::make('payee')
                    ->label('المستفيد')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Expense::STATUSES[$state] ?? $state)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Expense::STATUSES),
                SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(Expense::PAYMENT_METHODS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
