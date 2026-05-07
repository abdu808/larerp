<?php

namespace App\Filament\Resources\Beneficiaries\Schemas;

use App\Models\Beneficiary;
use App\Models\Family;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BeneficiaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('family_id')
                    ->label('العائلة')
                    ->relationship('family', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Family $record): string => "{$record->code} - {$record->name}")
                    ->searchable(['code', 'name', 'guardian_name'])
                    ->preload()
                    ->required(),
                TextInput::make('national_id')->label('رقم الهوية')->rule('digits:10')->unique(ignoreRecord: true),
                TextInput::make('first_name')->label('الاسم الأول')->required()->maxLength(255),
                TextInput::make('father_name')->label('اسم الأب')->maxLength(255),
                TextInput::make('grandfather_name')->label('اسم الجد')->maxLength(255),
                TextInput::make('family_name')->label('اسم العائلة')->maxLength(255),
                Select::make('gender')->label('الجنس')->options(Beneficiary::GENDER_OPTIONS),
                DatePicker::make('birth_date')->label('تاريخ الميلاد')->maxDate(now()),
                TextInput::make('phone')->label('الجوال')->tel()->maxLength(255),
                TextInput::make('relationship_to_guardian')->label('صلة القرابة برب الأسرة')->maxLength(255),
                Select::make('marital_status')->label('الحالة الاجتماعية')->options(Beneficiary::MARITAL_STATUS_OPTIONS),
                TextInput::make('education_level')->label('المستوى التعليمي')->maxLength(255),
                TextInput::make('employment_status')->label('الحالة الوظيفية')->maxLength(255),
                TextInput::make('health_status')->label('الحالة الصحية')->maxLength(255),
                Toggle::make('is_primary_contact')->label('جهة التواصل الأساسية')->default(false),
            ]);
    }
}
