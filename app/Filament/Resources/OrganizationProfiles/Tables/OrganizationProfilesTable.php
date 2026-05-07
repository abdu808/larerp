<?php

namespace App\Filament\Resources\OrganizationProfiles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الجمعية')
                    ->searchable(),
                TextColumn::make('legal_name')
                    ->label('الاسم النظامي')
                    ->searchable(),
                TextColumn::make('charity_type')
                    ->label('نوع الجمعية')
                    ->searchable(),
                TextColumn::make('license_number')
                    ->label('رقم الترخيص')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('الجوال')
                    ->searchable(),
                TextColumn::make('primary_color')
                    ->label('اللون الرئيسي')
                    ->searchable(),
                TextColumn::make('secondary_color')
                    ->label('اللون الثانوي')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('أضيف في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
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
