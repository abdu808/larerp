<?php

namespace App\Filament\Resources\SocialCaseNotes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SocialCaseNoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('social_case_id')->label('الحالة')->relationship('socialCase', 'case_number')->searchable()->preload()->required(),
                Select::make('user_id')->label('كاتب الملاحظة')->relationship('user', 'name')->searchable()->preload(),
                Select::make('type')->label('نوع الملاحظة')->required()->default('general')->options([
                    'general' => 'عامة',
                    'visit' => 'زيارة ميدانية',
                    'call' => 'اتصال',
                    'decision' => 'قرار',
                ]),
                DateTimePicker::make('noted_at')->label('وقت الملاحظة'),
                Textarea::make('note')->label('الملاحظة')->required()->columnSpanFull(),
            ]);
    }
}
