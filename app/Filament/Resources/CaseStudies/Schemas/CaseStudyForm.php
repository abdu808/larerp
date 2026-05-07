<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Models\CaseStudy;
use App\Models\SocialCase;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('social_case_id')
                    ->label('الحالة الاجتماعية')
                    ->relationship('socialCase', 'case_number')
                    ->getOptionLabelFromRecordUsing(fn (SocialCase $record): string => "{$record->case_number} - {$record->summary}")
                    ->searchable(['case_number', 'summary'])
                    ->preload()
                    ->required(),
                TextInput::make('assistance_request_id')->label('طلب الخدمة')->numeric()->minValue(1),
                Select::make('researcher_id')
                    ->label('الباحث')
                    ->relationship('researcher', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('supervisor_id')
                    ->label('المشرف')
                    ->relationship('supervisor', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')->label('الحالة')->required()->default(CaseStudy::STATUS_DRAFT)->options(CaseStudy::STATUS_OPTIONS),
                DatePicker::make('started_at')->label('تاريخ البدء'),
                Textarea::make('summary')->label('ملخص الدراسة')->columnSpanFull(),
                Textarea::make('family_situation')->label('وضع الأسرة')->columnSpanFull(),
                Textarea::make('risk_factors')->label('عوامل الخطورة')->columnSpanFull(),
                Textarea::make('recommendation')->label('توصية الباحث')->columnSpanFull(),
                Textarea::make('supervisor_notes')->label('ملاحظات المشرف')->columnSpanFull(),
            ]);
    }
}
