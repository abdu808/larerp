<?php

namespace App\Filament\Resources\Families\Schemas;

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
                TextInput::make('code')->label('رقم العائلة')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('اسم العائلة')->required(),
                TextInput::make('guardian_name')->label('اسم رب الأسرة')->required(),
                TextInput::make('phone')->label('الجوال')->tel(),
                TextInput::make('city')->label('المدينة'),
                TextInput::make('district')->label('الحي'),
                Select::make('status')->label('الحالة')->required()->default('active')->options([
                    'active' => 'نشطة',
                    'inactive' => 'غير نشطة',
                    'archived' => 'مؤرشفة',
                ]),
                DatePicker::make('registered_at')->label('تاريخ التسجيل'),
                Textarea::make('address')->label('العنوان')->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
