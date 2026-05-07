<?php

namespace App\Filament\Resources\FieldVisits\Schemas;

use App\Models\Beneficiary;
use App\Models\CaseStudy;
use App\Models\FieldVisit;
use App\Models\SocialCase;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FieldVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('beneficiary_id')
                    ->label('ملف المستفيد')
                    ->relationship('beneficiary', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => "{$record->file_number} - {$record->full_name}")
                    ->searchable(['file_number', 'first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                    ->preload(),
                Select::make('case_study_id')
                    ->label('دراسة الحالة')
                    ->relationship('caseStudy', 'id')
                    ->getOptionLabelFromRecordUsing(fn (CaseStudy $record): string => "#{$record->id} - {$record->socialCase?->case_number}")
                    ->searchable()
                    ->preload(),
                Select::make('social_case_id')
                    ->label('الحالة الاجتماعية')
                    ->relationship('socialCase', 'case_number')
                    ->getOptionLabelFromRecordUsing(fn (SocialCase $record): string => "{$record->case_number} - {$record->summary}")
                    ->searchable(['case_number', 'summary'])
                    ->preload(),
                Select::make('visitor_id')
                    ->label('الباحث')
                    ->relationship('visitor', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload(),
                Select::make('status')->label('الحالة')->required()->default(FieldVisit::STATUS_SCHEDULED)->options(FieldVisit::STATUS_OPTIONS),
                Select::make('type')->label('نوع الزيارة')->required()->default(FieldVisit::TYPE_FIELD)->options(FieldVisit::TYPE_OPTIONS),
                Toggle::make('is_urgent')->label('حالة عاجلة/خطرة')->default(false),
                DateTimePicker::make('scheduled_at')->label('موعد الزيارة'),
                DateTimePicker::make('completed_at')->label('وقت الإكمال'),
                TextInput::make('location')->label('الموقع/رابط الموقع')->maxLength(255)->columnSpanFull(),
                Select::make('building_status')
                    ->label('حالة المبنى')
                    ->options([
                        'excellent' => 'ممتاز',
                        'good' => 'جيد',
                        'average' => 'متوسط',
                        'poor' => 'سيئ',
                        'dilapidated' => 'متهالك',
                    ]),
                Select::make('furniture_status')
                    ->label('حالة الأثاث')
                    ->options([
                        'good' => 'جيد',
                        'average' => 'متوسط',
                        'poor' => 'سيئ',
                        'need_replacement' => 'يحتاج استبدال',
                    ]),
                Textarea::make('purpose')->label('الغرض')->columnSpanFull(),
                Textarea::make('findings')->label('النتائج')->columnSpanFull(),
                Textarea::make('recommendations')->label('التوصيات')->columnSpanFull(),
                Textarea::make('next_action')->label('الإجراء التالي')->columnSpanFull(),
            ]);
    }
}
