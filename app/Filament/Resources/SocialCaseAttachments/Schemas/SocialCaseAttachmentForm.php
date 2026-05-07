<?php

namespace App\Filament\Resources\SocialCaseAttachments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SocialCaseAttachmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('social_case_id')->label('الحالة')->relationship('socialCase', 'case_number')->searchable(['case_number', 'summary'])->preload()->required(),
                Select::make('uploaded_by_id')->label('رفع بواسطة')->relationship('uploadedBy', 'name')->searchable()->preload(),
                TextInput::make('title')->label('عنوان المرفق')->required()->maxLength(255),
                TextInput::make('file_path')->label('مسار الملف')->required()->maxLength(255),
                TextInput::make('mime_type')->label('نوع الملف')->maxLength(255),
                TextInput::make('size')->label('الحجم بالبايت')->numeric()->minValue(0),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
