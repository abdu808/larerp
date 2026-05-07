<?php

namespace App\Filament\Resources\NeedsAssessments\Schemas;

use App\Models\CaseStudy;
use App\Models\NeedsAssessment;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NeedsAssessmentForm
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
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('assessed_by_id')
                    ->label('المقيّم')
                    ->relationship('assessedBy', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload(),
                TextInput::make('income_score')->label('الدخل / 25')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['income_score'])->default(0)->required(),
                TextInput::make('vulnerability_score')->label('الهشاشة / 20')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['vulnerability_score'])->default(0)->required(),
                TextInput::make('housing_score')->label('السكن / 15')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['housing_score'])->default(0)->required(),
                TextInput::make('health_score')->label('الصحة / 15')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['health_score'])->default(0)->required(),
                TextInput::make('education_score')->label('التعليم / 10')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['education_score'])->default(0)->required(),
                TextInput::make('debts_score')->label('الديون / 10')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['debts_score'])->default(0)->required(),
                TextInput::make('support_sources_score')->label('مصادر الدعم / 5')->numeric()->minValue(0)->maxValue(NeedsAssessment::WEIGHTS['support_sources_score'])->default(0)->required(),
                TextInput::make('total_score')->label('المجموع')->numeric()->disabled()->dehydrated(false),
                Select::make('level')->label('المستوى')->options(NeedsAssessment::LEVEL_OPTIONS)->disabled()->dehydrated(false),
                DateTimePicker::make('assessed_at')->label('وقت التقييم'),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
