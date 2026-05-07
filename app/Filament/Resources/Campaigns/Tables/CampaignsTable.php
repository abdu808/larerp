<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Models\Campaign;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('اسم الحملة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.title')
                    ->label('المشروع')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('channel')
                    ->label('القناة')
                    ->searchable(),
                TextColumn::make('goal_amount')
                    ->label('المستهدف')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('collected_amount')
                    ->label('المحصل')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('progress_percentage')
                    ->label('نسبة الإنجاز')
                    ->formatStateUsing(fn (int $state): string => $state.'%')
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw(
                        'CASE WHEN goal_amount > 0 THEN collected_amount / goal_amount ELSE 0 END '.$direction
                    )),
                IconColumn::make('is_featured')
                    ->label('مميزة')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('أضيفت في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Campaign::STATUSES),
                SelectFilter::make('channel')
                    ->label('القناة')
                    ->options(Campaign::CHANNELS),
                TernaryFilter::make('is_featured')
                    ->label('مميزة'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
