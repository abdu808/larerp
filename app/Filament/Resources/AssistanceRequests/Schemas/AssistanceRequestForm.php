<?php

namespace App\Filament\Resources\AssistanceRequests\Schemas;

use App\Models\AssistanceRequest;
use App\Models\Beneficiary;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AssistanceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('request_number')->label('رقم الطلب')->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('family_id')
                    ->label('العائلة')
                    ->relationship('family', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Family $record): string => "{$record->code} - {$record->name}")
                    ->searchable(['code', 'name', 'guardian_name'])
                    ->preload(),
                Select::make('beneficiary_id')
                    ->label('المستفيد')
                    ->relationship('beneficiary', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => $record->full_name)
                    ->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                    ->preload(),
                Select::make('social_case_id')
                    ->label('الحالة الاجتماعية')
                    ->relationship('socialCase', 'case_number')
                    ->getOptionLabelFromRecordUsing(fn (SocialCase $record): string => "{$record->case_number} - {$record->summary}")
                    ->searchable(['case_number', 'summary'])
                    ->preload(),
                Select::make('assigned_to_id')
                    ->label('المحال إليه')
                    ->relationship('assignedTo', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable(['name', 'email'])
                    ->preload(),
                Select::make('request_type')->label('نوع الطلب')->required()->options(AssistanceRequest::TYPE_OPTIONS)->searchable(),
                Select::make('status')->label('الحالة')->required()->default(AssistanceRequest::STATUS_DRAFT)->options(AssistanceRequest::STATUS_OPTIONS),
                Select::make('urgency')->label('درجة العجلة')->required()->default('normal')->options(AssistanceRequest::URGENCY_OPTIONS),
                TextInput::make('source')->label('مصدر الطلب')->maxLength(255),
                DateTimePicker::make('submitted_at')->label('تاريخ التقديم'),
                Toggle::make('consent_to_store_data')->label('موافقة حفظ البيانات')->required(),
                Textarea::make('description')->label('وصف مختصر')->required()->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
