<?php

namespace App\Filament\Resources\Campaigns\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
