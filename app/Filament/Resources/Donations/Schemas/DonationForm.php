<?php

namespace App\Filament\Resources\Donations\Schemas;

use App\Models\Donation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('المشروع')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload(),
                Select::make('campaign_id')
                    ->label('الحملة')
                    ->relationship('campaign', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('donor_name')
                    ->label('اسم المتبرع')
                    ->maxLength(255),
                TextInput::make('donor_email')
                    ->label('بريد المتبرع')
                    ->email()
                    ->maxLength(255),
                TextInput::make('donor_phone')
                    ->label('جوال المتبرع')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->required()
                    ->numeric()
                    ->minValue(0.01),
                TextInput::make('currency')
                    ->label('العملة')
                    ->required()
                    ->default('SAR')
                    ->length(3)
                    ->regex('/^[A-Z]{3}$/'),
                Select::make('payment_status')
                    ->label('حالة الدفع')
                    ->required()
                    ->default(Donation::STATUS_PENDING)
                    ->options(Donation::PAYMENT_STATUSES),
                Select::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(Donation::PAYMENT_METHODS)
                    ->searchable(),
                TextInput::make('reference')
                    ->label('المرجع')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('donated_at')
                    ->label('تاريخ التبرع'),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }
}
