<?php

namespace App\Filament\Resources\Beneficiaries\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BeneficiariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('الاسم')->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name']),
                TextColumn::make('national_id')->label('رقم الهوية')->searchable(),
                TextColumn::make('family.name')->label('العائلة')->searchable(),
                TextColumn::make('gender')->label('الجنس')->badge(),
                TextColumn::make('phone')->label('الجوال')->searchable(),
                TextColumn::make('relationship_to_guardian')->label('صلة القرابة')->searchable(),
                IconColumn::make('is_primary_contact')->label('تواصل أساسي')->boolean(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
