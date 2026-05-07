<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('اسم المشروع')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
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
                    ->label('مميز')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('أضيف في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Project::STATUSES),
                SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options([
                        'relief' => 'إغاثي',
                        'health' => 'صحي',
                        'education' => 'تعليمي',
                        'housing' => 'إسكان',
                        'general' => 'عام',
                    ]),
                TernaryFilter::make('is_featured')
                    ->label('مميز'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
