<?php

namespace App\Filament\Resources\Beneficiaries\Tables;

use App\Models\Beneficiary;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BeneficiariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('الاسم')->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name']),
                TextColumn::make('national_id')->label('رقم الهوية')->searchable(),
                TextColumn::make('fileOwner.full_name')->label('يتبع ملف')->placeholder('صاحب الملف'),
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Beneficiary::genderLabelFor($state))
                    ->color(fn (?string $state): string => $state === 'female' ? 'danger' : 'info'),
                TextColumn::make('phone')->label('الجوال')->searchable(),
                TextColumn::make('relationship_to_guardian')->label('صلة القرابة بصاحب الملف')->searchable(),
                TextColumn::make('social_cases_count')->label('الحالات')->counts('socialCases')->sortable(),
                TextColumn::make('dependents_count')->label('التابعون')->counts('dependents')->sortable(),
                IconColumn::make('is_primary_contact')->label('تواصل أساسي')->boolean(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('file_owner_id')->label('يتبع ملف')->relationship('fileOwner', 'first_name')->searchable()->preload(),
                SelectFilter::make('gender')->label('الجنس')->options(Beneficiary::GENDER_OPTIONS),
                SelectFilter::make('marital_status')->label('الحالة الاجتماعية')->options(Beneficiary::MARITAL_STATUS_OPTIONS),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (Beneficiary $record): bool => ! $record->canBeDeleted()),
            ]);
    }
}
