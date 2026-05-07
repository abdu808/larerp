<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('المشروع')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('اسم الحملة')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('رمز الحملة')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('status')
                    ->label('الحالة')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'مسودة',
                        'active' => 'نشطة',
                        'paused' => 'متوقفة مؤقتًا',
                        'completed' => 'مكتملة',
                    ]),
                Select::make('channel')
                    ->label('القناة')
                    ->options([
                        'website' => 'الموقع',
                        'social' => 'الشبكات الاجتماعية',
                        'branch' => 'الفرع',
                        'manual' => 'يدوي',
                    ])
                    ->searchable(),
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
                DateTimePicker::make('starts_at')
                    ->label('بداية الحملة'),
                DateTimePicker::make('ends_at')
                    ->label('نهاية الحملة'),
                Toggle::make('is_featured')
                    ->label('مميزة')
                    ->default(false),
                TextInput::make('sort_order')
                    ->label('ترتيب العرض')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Textarea::make('description')
                    ->label('وصف الحملة')
                    ->columnSpanFull(),
            ]);
    }
}
