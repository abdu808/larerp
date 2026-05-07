<?php

namespace App\Filament\Resources\Beneficiaries\RelationManagers;

use App\Models\Beneficiary;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FileMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'dependents';

    protected static ?string $title = 'المستفيد والتابعون';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('الاسم')->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name']),
                TextColumn::make('national_id')->label('رقم الهوية')->searchable(),
                TextColumn::make('relationship_to_guardian')->label('صلة القرابة بصاحب الملف')->placeholder('صاحب الملف'),
                TextColumn::make('phone')->label('الجوال')->searchable(),
                IconColumn::make('is_primary_contact')->label('تواصل أساسي')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة تابع')
                    ->mutateDataUsing(function (array $data): array {
                        $data['file_owner_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->schema([
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
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
