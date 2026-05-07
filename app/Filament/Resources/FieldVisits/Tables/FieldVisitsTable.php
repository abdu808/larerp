<?php

namespace App\Filament\Resources\FieldVisits\Tables;

use App\Models\FieldVisit;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FieldVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('beneficiary.full_name')->label('المستفيد')->searchable()->sortable(),
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('visitor.name')->label('الزائر')->searchable()->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => FieldVisit::typeLabelFor($state)),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => FieldVisit::statusLabelFor($state))
                    ->color(fn (?string $state): string => FieldVisit::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('scheduled_at')->label('الموعد')->dateTime()->sortable(),
                TextColumn::make('completed_at')->label('الإكمال')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('building_status')->label('حالة المبنى')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('furniture_status')->label('حالة الأثاث')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location')->label('الموقع')->searchable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(FieldVisit::STATUS_OPTIONS),
                SelectFilter::make('type')->label('النوع')->options(FieldVisit::TYPE_OPTIONS),
                SelectFilter::make('visitor_id')->label('الزائر')->relationship('visitor', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
