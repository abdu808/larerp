<?php

namespace App\Filament\Resources\Beneficiaries\Schemas;

use App\Models\Beneficiary;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BeneficiaryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('نظرة عامة على ملف المستفيد')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('file_number')->label('رقم الملف')->placeholder('-'),
                        TextEntry::make('full_name')->label('الاسم')->placeholder('-'),
                        TextEntry::make('national_id')->label('رقم الهوية')->placeholder('-'),
                        TextEntry::make('phone')->label('الجوال')->placeholder('-'),
                        TextEntry::make('status')
                            ->label('حالة الملف')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Beneficiary::statusLabelFor($state))
                            ->color(fn (?string $state): string => Beneficiary::statusColorFor($state)),
                        TextEntry::make('classification')
                            ->label('التصنيف')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Beneficiary::classificationLabelFor($state))
                            ->color(fn (?string $state): string => Beneficiary::classificationColorFor($state)),
                        TextEntry::make('score')->label('درجة الاحتياج')->placeholder('0'),
                        TextEntry::make('file_members_count')->label('عدد أفراد الملف'),
                    ]),
                Section::make('السكن والوضع المالي')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('city')->label('المدينة')->placeholder('-'),
                        TextEntry::make('district')->label('الحي')->placeholder('-'),
                        TextEntry::make('housing_type')->label('نوع السكن')->placeholder('-'),
                        TextEntry::make('registered_at')->label('تاريخ التسجيل')->date()->placeholder('-'),
                        TextEntry::make('total_income')->label('إجمالي الدخل')->money('SAR'),
                        TextEntry::make('total_expenses')->label('إجمالي المصروفات')->money('SAR'),
                        TextEntry::make('status_reason')->label('سبب الإيقاف/الأرشفة')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('notes')->label('ملاحظات الباحث')->placeholder('-')->columnSpanFull(),
                    ]),
            ]);
    }
}
