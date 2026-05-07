<?php

namespace App\Filament\Resources\Families\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('status')->label('الحالة')->badge(),
                TextColumn::make('beneficiaries_count')->label('عدد المستفيدين')->counts('beneficiaries')->sortable(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
