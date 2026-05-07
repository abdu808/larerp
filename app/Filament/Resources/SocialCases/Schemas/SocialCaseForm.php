<?php

namespace App\Filament\Resources\SocialCases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SocialCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('case_number')->label('رقم الحالة')->required()->unique(ignoreRecord: true),
                Select::make('family_id')->label('العائلة')->relationship('family', 'name')->searchable()->preload()->required(),
                Select::make('beneficiary_id')->label('المستفيد المرتبط')->relationship('beneficiary', 'first_name')->searchable()->preload(),
                Select::make('type')->label('نوع الحالة')->options([
                    'financial' => 'احتياج مالي',
                    'housing' => 'سكن',
                    'health' => 'صحي',
                    'education' => 'تعليمي',
                    'emergency' => 'طارئ',
                ])->searchable(),
                Select::make('status')->label('الحالة')->required()->default('open')->options([
                    'open' => 'مفتوحة',
                    'under_review' => 'قيد الدراسة',
                    'approved' => 'معتمدة',
                    'closed' => 'مغلقة',
                ]),
                Select::make('priority')->label('الأولوية')->required()->default('normal')->options([
                    'low' => 'منخفضة',
                    'normal' => 'عادية',
                    'high' => 'عالية',
                    'urgent' => 'عاجلة',
                ]),
                DatePicker::make('opened_at')->label('تاريخ الفتح'),
                DatePicker::make('closed_at')->label('تاريخ الإغلاق'),
                TextInput::make('monthly_income')->label('الدخل الشهري')->numeric(),
                TextInput::make('monthly_expenses')->label('المصروفات الشهرية')->numeric(),
                Textarea::make('summary')->label('ملخص الحالة')->required()->columnSpanFull(),
                Textarea::make('needs')->label('الاحتياجات')->columnSpanFull(),
            ]);
    }
}
