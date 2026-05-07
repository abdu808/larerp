<?php

namespace App\Filament\Resources\Families\Tables;

use App\Models\Family;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FamiliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('رقم العائلة')->searchable()->sortable(),
                TextColumn::make('name')->label('اسم العائلة')->searchable(),
                TextColumn::make('guardian_name')->label('رب الأسرة')->searchable(),
                TextColumn::make('phone')->label('الجوال')->searchable(),
                TextColumn::make('city')->label('المدينة')->searchable(),
                TextColumn::make('district')->label('الحي')->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Family::statusLabelFor($state))
                    ->color(fn (?string $state): string => Family::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('beneficiaries_count')->label('عدد المستفيدين')->counts('beneficiaries')->sortable(),
                TextColumn::make('social_cases_count')->label('الحالات')->counts('socialCases')->sortable(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Family::STATUS_OPTIONS),
                SelectFilter::make('city')
                    ->label('المدينة')
                    ->options(fn (): array => Family::query()
                        ->whereNotNull('city')
                        ->distinct()
                        ->orderBy('city')
                        ->pluck('city', 'city')
                        ->all())
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (Family $record): bool => ! $record->canBeDeleted()),
            ]);
    }
}
