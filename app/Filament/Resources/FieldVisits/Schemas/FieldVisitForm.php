<?php

namespace App\Filament\Resources\FieldVisits\Schemas;

use App\Models\CaseStudy;
use App\Models\FieldVisit;
use App\Models\SocialCase;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FieldVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('case_study_id')
                    ->label('دراسة الحالة')
                    ->relationship('caseStudy', 'id')
                    ->getOptionLabelFromRecordUsing(fn (CaseStudy $record): string => "#{$record->id} - {$record->socialCase?->case_number}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('social_case_id')
                    ->label('الحالة الاجتماعية')
                    ->relationship('socialCase', 'case_number')
                    ->getOptionLabelFromRecordUsing(fn (SocialCase $record): string => "{$record->case_number} - {$record->summary}")
                    ->searchable(['case_number', 'summary'])
                    ->preload()
                    ->required(),
                Select::make('visitor_id')
                    ->label('الزائر')
                    ->relationship('visitor', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload(),
                Select::make('status')->label('الحالة')->required()->default(FieldVisit::STATUS_SCHEDULED)->options(FieldVisit::STATUS_OPTIONS),
                Select::make('type')->label('نوع الزيارة')->required()->default(FieldVisit::TYPE_FIELD)->options(FieldVisit::TYPE_OPTIONS),
                DateTimePicker::make('scheduled_at')->label('موعد الزيارة'),
                DateTimePicker::make('completed_at')->label('وقت الإكمال'),
                TextInput::make('location')->label('الموقع')->maxLength(255),
                Textarea::make('purpose')->label('الغرض')->columnSpanFull(),
                Textarea::make('findings')->label('النتائج')->columnSpanFull(),
                Textarea::make('next_action')->label('الإجراء التالي')->columnSpanFull(),
            ]);
    }
}
