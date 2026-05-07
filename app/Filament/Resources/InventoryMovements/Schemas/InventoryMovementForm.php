<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use App\Models\InventoryMovement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_item_id')
                    ->label('الصنف')
                    ->relationship('inventoryItem', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->label('نوع الحركة')
                    ->required()
                    ->options(InventoryMovement::TYPES),
                TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                DatePicker::make('movement_date')
                    ->label('تاريخ الحركة')
                    ->default(now())
                    ->required(),
                TextInput::make('reference_type')
                    ->label('نوع المرجع')
                    ->maxLength(255),
                TextInput::make('reference_number')
                    ->label('رقم المرجع')
                    ->maxLength(255),
                TextInput::make('unit_cost')
                    ->label('تكلفة الوحدة')
                    ->numeric()
                    ->minValue(0),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }
}
