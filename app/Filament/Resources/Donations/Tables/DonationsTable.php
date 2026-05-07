<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Models\Donation;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable(),
                TextColumn::make('donor_name')
                    ->label('المتبرع')
                    ->searchable(),
                TextColumn::make('project.title')
                    ->label('المشروع')
                    ->searchable(),
                TextColumn::make('campaign.title')
                    ->label('الحملة')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->searchable(),
                TextColumn::make('donated_at')
                    ->label('تاريخ التبرع')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('أضيف في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options(Donation::PAYMENT_STATUSES),
                SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(Donation::PAYMENT_METHODS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
