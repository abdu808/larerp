<?php

namespace App\Filament\Resources\Beneficiaries\Schemas;

use App\Models\Beneficiary;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BeneficiaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ملف المستفيد 360')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('البيانات الأساسية')
                            ->schema([
                                Section::make('تعريف الملف')
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('file_number')->label('رقم الملف')->disabled()->dehydrated(false),
                                        Select::make('file_owner_id')
                                            ->label('يتبع ملف')
                                            ->relationship('fileOwner', 'first_name')
                                            ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => $record->full_name)
                                            ->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                                            ->preload()
                                            ->helperText('اتركه فارغا لصاحب الملف. استخدمه فقط عند إنشاء تابع داخل ملف مستفيد.'),
                                        Select::make('status')
                                            ->label('حالة الملف')
                                            ->required()
                                            ->default(Beneficiary::STATUS_ACTIVE)
                                            ->options(Beneficiary::STATUS_OPTIONS),
                                        DatePicker::make('registered_at')->label('تاريخ التسجيل')->maxDate(now()),
                                        Textarea::make('status_reason')->label('سبب الإيقاف/الأرشفة')->columnSpanFull(),
                                    ]),
                                Section::make('بيانات الهوية والتواصل')
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('national_id')->label('رقم الهوية')->rule('digits:10')->unique(ignoreRecord: true),
                                        TextInput::make('first_name')->label('الاسم الأول')->required()->maxLength(255),
                                        TextInput::make('father_name')->label('اسم الأب')->maxLength(255),
                                        TextInput::make('grandfather_name')->label('اسم الجد')->maxLength(255),
                                        TextInput::make('family_name')->label('الاسم الأخير')->maxLength(255),
                                        Select::make('gender')->label('الجنس')->options(Beneficiary::GENDER_OPTIONS),
                                        DatePicker::make('birth_date')->label('تاريخ الميلاد')->maxDate(now()),
                                        TextInput::make('nationality')->label('الجنسية')->maxLength(255),
                                        TextInput::make('phone')->label('الجوال')->tel()->maxLength(255),
                                        Select::make('marital_status')->label('الحالة الاجتماعية')->options(Beneficiary::MARITAL_STATUS_OPTIONS),
                                        TextInput::make('relationship_to_guardian')->label('صلة القرابة بصاحب الملف')->maxLength(255),
                                        Toggle::make('is_primary_contact')->label('جهة التواصل الأساسية')->default(false),
                                    ]),
                                Section::make('السكن والنطاق الجغرافي')
                                    ->columns(3)
                                    ->schema([
                                        Select::make('housing_type')
                                            ->label('نوع السكن')
                                            ->options([
                                                'owned' => 'ملك',
                                                'rented' => 'إيجار',
                                                'hosted' => 'مستضاف',
                                                'heir' => 'ورثة',
                                                'waqf' => 'وقف',
                                                'other' => 'أخرى',
                                            ]),
                                        TextInput::make('city')->label('المدينة')->maxLength(255),
                                        TextInput::make('district')->label('الحي')->maxLength(255),
                                        TextInput::make('location_url')->label('رابط الموقع')->url()->columnSpanFull(),
                                        Textarea::make('address')->label('العنوان التفصيلي')->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('الوضع المالي والتصنيف')
                            ->schema([
                                Section::make('مصادر الدخل الشهرية')
                                    ->columns(5)
                                    ->schema([
                                        TextInput::make('salary_income')->label('راتب/عمل')->numeric()->minValue(0)->default(0),
                                        TextInput::make('social_security_income')->label('الضمان')->numeric()->minValue(0)->default(0),
                                        TextInput::make('citizen_account_income')->label('حساب المواطن')->numeric()->minValue(0)->default(0),
                                        TextInput::make('retirement_income')->label('التقاعد')->numeric()->minValue(0)->default(0),
                                        TextInput::make('other_income')->label('دخل آخر')->numeric()->minValue(0)->default(0),
                                    ]),
                                Section::make('المصروفات الشهرية')
                                    ->columns(5)
                                    ->schema([
                                        TextInput::make('rent_expense')->label('الإيجار')->numeric()->minValue(0)->default(0),
                                        TextInput::make('electricity_expense')->label('الكهرباء')->numeric()->minValue(0)->default(0),
                                        TextInput::make('water_expense')->label('المياه')->numeric()->minValue(0)->default(0),
                                        TextInput::make('loans_expense')->label('القروض/الديون')->numeric()->minValue(0)->default(0),
                                        TextInput::make('treatment_expense')->label('العلاج')->numeric()->minValue(0)->default(0),
                                    ]),
                                Section::make('نتيجة الأهلية')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('score')->label('درجة الاحتياج')->disabled()->dehydrated(false),
                                        Select::make('classification')
                                            ->label('التصنيف')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->options([
                                                Beneficiary::CLASSIFICATION_A => 'A - احتياج عال',
                                                Beneficiary::CLASSIFICATION_B => 'B - احتياج متوسط',
                                                Beneficiary::CLASSIFICATION_C => 'C - احتياج منخفض',
                                                Beneficiary::CLASSIFICATION_D => 'D - غير مستحق حاليا',
                                                Beneficiary::CLASSIFICATION_EXCLUDED => 'X - مستبعد',
                                            ]),
                                        DatePicker::make('expiry_date')->label('تاريخ انتهاء الملف المؤقت'),
                                    ]),
                            ]),
                        Tab::make('الدراسة الاجتماعية')
                            ->schema([
                                Section::make('التعليم والعمل والصحة')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('education_level')->label('المستوى التعليمي')->maxLength(255),
                                        TextInput::make('employment_status')->label('الحالة الوظيفية')->maxLength(255),
                                        TextInput::make('health_status')->label('الحالة الصحية')->maxLength(255),
                                    ]),
                                Section::make('بيانات بنكية')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('bank_name')->label('اسم البنك')->maxLength(255),
                                        TextInput::make('iban')->label('IBAN')->maxLength(34),
                                    ]),
                                Textarea::make('notes')->label('ملاحظات الباحث الاجتماعي')->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
