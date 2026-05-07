<?php

namespace App\Filament\Resources\SocialCases\Schemas;

use App\Models\Beneficiary;
use App\Models\SocialCase;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SocialCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('case_number')->label('رقم الحالة')->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('beneficiary_id')
                    ->label('المستفيد المرتبط')
                    ->relationship('beneficiary', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => $record->full_name)
                    ->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                    ->preload(),
                Select::make('type')->label('نوع الحالة')->options(SocialCase::TYPE_OPTIONS)->searchable(),
                Select::make('status')->label('الحالة')->required()->default(SocialCase::STATUS_OPEN)->options(SocialCase::STATUS_OPTIONS),
                Select::make('priority')->label('الأولوية')->required()->default('normal')->options(SocialCase::PRIORITY_OPTIONS),
                DatePicker::make('opened_at')->label('تاريخ الفتح'),
                DatePicker::make('closed_at')->label('تاريخ الإغلاق'),
                TextInput::make('monthly_income')->label('الدخل الشهري')->numeric()->minValue(0),
                TextInput::make('monthly_expenses')->label('المصروفات الشهرية')->numeric()->minValue(0),
                Textarea::make('summary')->label('ملخص الحالة')->required()->columnSpanFull(),
                Textarea::make('needs')->label('الاحتياجات')->columnSpanFull(),
            ]);
    }
}
