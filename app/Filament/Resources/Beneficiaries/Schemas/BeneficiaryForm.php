<?php

namespace App\Filament\Resources\Beneficiaries\Schemas;

use App\Models\Beneficiary;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BeneficiaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('file_owner_id')
                    ->label('يتبع ملف')
                    ->relationship('fileOwner', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => $record->full_name)
                    ->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                    ->preload()
                    ->helperText('اتركه فارغا إذا كان هذا هو صاحب ملف المستفيد. اختر صاحب الملف فقط عند إضافة تابع.'),
                TextInput::make('national_id')->label('رقم الهوية')->rule('digits:10')->unique(ignoreRecord: true),
                TextInput::make('first_name')->label('الاسم الأول')->required()->maxLength(255),
                TextInput::make('father_name')->label('اسم الأب')->maxLength(255),
                TextInput::make('grandfather_name')->label('اسم الجد')->maxLength(255),
                TextInput::make('family_name')->label('الاسم الأخير')->maxLength(255),
                Select::make('gender')->label('الجنس')->options(Beneficiary::GENDER_OPTIONS),
                DatePicker::make('birth_date')->label('تاريخ الميلاد')->maxDate(now()),
                TextInput::make('phone')->label('الجوال')->tel()->maxLength(255),
                TextInput::make('relationship_to_guardian')->label('صلة القرابة بصاحب الملف')->maxLength(255),
                Select::make('marital_status')->label('الحالة الاجتماعية')->options(Beneficiary::MARITAL_STATUS_OPTIONS),
                TextInput::make('education_level')->label('المستوى التعليمي')->maxLength(255),
                TextInput::make('employment_status')->label('الحالة الوظيفية')->maxLength(255),
                TextInput::make('health_status')->label('الحالة الصحية')->maxLength(255),
                Toggle::make('is_primary_contact')->label('جهة التواصل الأساسية')->default(false),
                Select::make('status')->label('حالة الملف')->required()->default(Beneficiary::STATUS_ACTIVE)->options(Beneficiary::STATUS_OPTIONS),
                DatePicker::make('registered_at')->label('تاريخ التسجيل')->maxDate(now()),
                TextInput::make('city')->label('المدينة')->maxLength(255),
                TextInput::make('district')->label('الحي')->maxLength(255),
                Textarea::make('address')->label('العنوان')->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
