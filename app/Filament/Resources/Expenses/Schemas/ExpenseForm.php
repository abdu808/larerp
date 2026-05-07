<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('financial_account_id')
                    ->label('الحساب المالي')
                    ->relationship('financialAccount', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('category')
                    ->label('التصنيف')
                    ->maxLength(255),
                TextInput::make('payee')
                    ->label('المستفيد من الصرف')
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                DatePicker::make('expense_date')
                    ->label('تاريخ المصروف')
                    ->default(now())
                    ->required(),
                Select::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(Expense::PAYMENT_METHODS),
                TextInput::make('reference_number')
                    ->label('رقم المرجع')
                    ->maxLength(255),
                Select::make('status')
                    ->label('الحالة')
                    ->required()
                    ->default('paid')
                    ->options(Expense::STATUSES),
                Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),
            ]);
    }
}
