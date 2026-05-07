<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('اسم المشروع')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('رمز المشروع')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->label('التصنيف')
                    ->options([
                        'relief' => 'إغاثي',
                        'health' => 'صحي',
                        'education' => 'تعليمي',
                        'housing' => 'إسكان',
                        'general' => 'عام',
                    ])
                    ->searchable(),
                Select::make('status')
                    ->label('الحالة')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'مسودة',
                        'active' => 'نشط',
                        'paused' => 'متوقف مؤقتًا',
                        'completed' => 'مكتمل',
                    ]),
                TextInput::make('goal_amount')
                    ->label('المبلغ المستهدف')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('collected_amount')
                    ->label('المبلغ المحصل')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                DatePicker::make('starts_on')
                    ->label('تاريخ البداية'),
                DatePicker::make('ends_on')
                    ->label('تاريخ النهاية'),
                Toggle::make('is_featured')
                    ->label('مميز')
                    ->default(false),
                TextInput::make('sort_order')
                    ->label('ترتيب العرض')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Textarea::make('description')
                    ->label('وصف المشروع')
                    ->columnSpanFull(),
            ]);
    }
}
