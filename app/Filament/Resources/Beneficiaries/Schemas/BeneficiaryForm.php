<?php

namespace App\Filament\Resources\Beneficiaries\Schemas;

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
                Select::make('family_id')->label('العائلة')->relationship('family', 'name')->searchable()->preload()->required(),
                TextInput::make('national_id')->label('رقم الهوية')->unique(ignoreRecord: true),
                TextInput::make('first_name')->label('الاسم الأول')->required(),
                TextInput::make('father_name')->label('اسم الأب'),
                TextInput::make('grandfather_name')->label('اسم الجد'),
                TextInput::make('family_name')->label('اسم العائلة'),
                Select::make('gender')->label('الجنس')->options([
                    'male' => 'ذكر',
                    'female' => 'أنثى',
                ]),
                DatePicker::make('birth_date')->label('تاريخ الميلاد'),
                TextInput::make('phone')->label('الجوال')->tel(),
                TextInput::make('relationship_to_guardian')->label('صلة القرابة برب الأسرة'),
                Select::make('marital_status')->label('الحالة الاجتماعية')->options([
                    'single' => 'أعزب/عزباء',
                    'married' => 'متزوج/ة',
                    'divorced' => 'مطلق/ة',
                    'widowed' => 'أرمل/ة',
                ]),
                TextInput::make('education_level')->label('المستوى التعليمي'),
                TextInput::make('employment_status')->label('الحالة الوظيفية'),
                TextInput::make('health_status')->label('الحالة الصحية'),
                Toggle::make('is_primary_contact')->label('جهة التواصل الأساسية')->default(false),
            ]);
    }
}
