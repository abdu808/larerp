<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('رمز الصنف')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('اسم الصنف')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->label('التصنيف')
                    ->maxLength(255),
                TextInput::make('unit')
                    ->label('وحدة القياس')
                    ->default('piece')
                    ->required()
                    ->maxLength(50),
                TextInput::make('minimum_quantity')
                    ->label('حد التنبيه')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('current_quantity')
                    ->label('الكمية الحالية')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }
}
