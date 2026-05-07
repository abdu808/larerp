<?php

namespace App\Filament\Resources\SocialCaseAttachments\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialCaseAttachmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable(),
                TextColumn::make('title')->label('العنوان')->searchable(),
                TextColumn::make('file_path')->label('مسار الملف')->limit(45)->searchable(),
                TextColumn::make('mime_type')->label('نوع الملف')->searchable(),
                TextColumn::make('uploadedBy.name')->label('رفع بواسطة')->placeholder('-')->searchable(),
                TextColumn::make('created_at')->label('أضيف في')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
