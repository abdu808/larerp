<?php

namespace App\Filament\Resources\Families\Schemas;

use App\Models\Family;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FamilyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->label('رقم ملف المستفيد')->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('name')->label('اسم ملف المستفيد')->required()->maxLength(255),
                TextInput::make('guardian_name')->label('صاحب الملف الرئيسي')->required()->maxLength(255),
                TextInput::make('phone')->label('الجوال')->tel()->maxLength(255),
                TextInput::make('city')->label('المدينة')->maxLength(255),
                TextInput::make('district')->label('الحي')->maxLength(255),
                Select::make('status')->label('الحالة')->required()->default(Family::STATUS_ACTIVE)->options(Family::STATUS_OPTIONS),
                DatePicker::make('registered_at')->label('تاريخ التسجيل')->maxDate(now()),
                Textarea::make('address')->label('العنوان')->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
