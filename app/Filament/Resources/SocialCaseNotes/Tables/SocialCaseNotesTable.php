<?php

namespace App\Filament\Resources\SocialCaseNotes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialCaseNotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable(),
                TextColumn::make('type')->label('النوع')->badge(),
                TextColumn::make('user.name')->label('كاتب الملاحظة')->placeholder('-')->searchable(),
                TextColumn::make('note')->label('الملاحظة')->limit(60)->searchable(),
                TextColumn::make('noted_at')->label('وقت الملاحظة')->dateTime()->sortable(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
