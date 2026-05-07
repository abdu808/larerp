<?php

namespace App\Filament\Resources\FinancialAccounts\Schemas;

use App\Models\FinancialAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FinancialAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('رمز الحساب')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('اسم الحساب')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع الحساب')
                    ->required()
                    ->options(FinancialAccount::ACCOUNT_TYPES),
                TextInput::make('opening_balance')
                    ->label('الرصيد الافتتاحي')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('current_balance')
                    ->label('الرصيد الحالي')
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
