<?php

namespace App\Filament\Resources\OrganizationProfiles\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrganizationProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الجمعية')
                    ->required(),
                TextInput::make('legal_name')
                    ->label('الاسم النظامي'),
                Select::make('charity_type')
                    ->label('نوع الجمعية')
                    ->options([
                        'general_charity' => 'جمعية بر',
                        'housing' => 'جمعية إسكان',
                        'health' => 'جمعية صحية',
                        'education' => 'جمعية تعليمية',
                        'specialized' => 'جمعية تخصصية',
                    ])
                    ->searchable(),
                TextInput::make('license_number')
                    ->label('رقم الترخيص'),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email(),
                TextInput::make('phone')
                    ->label('الجوال')
                    ->tel(),
                TextInput::make('website_url')
                    ->label('الموقع الإلكتروني')
                    ->url(),
                TextInput::make('logo_path')
                    ->label('مسار الشعار'),
                TextInput::make('primary_color')
                    ->label('اللون الرئيسي')
                    ->required()
                    ->default('#0f766e'),
                TextInput::make('secondary_color')
                    ->label('اللون الثانوي')
                    ->required()
                    ->default('#334155'),
                Textarea::make('settings')
                    ->rules([self::validJsonRule()])
                    ->label('إعدادات JSON')
                    ->formatStateUsing(fn ($state): string => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->dehydrateStateUsing(fn (?string $state): array => json_decode($state ?: '[]', true))
                    ->columnSpanFull(),
                Textarea::make('active_modules')
                    ->rules([self::validJsonRule()])
                    ->label('الموديولات المفعلة JSON')
                    ->formatStateUsing(fn ($state): string => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->dehydrateStateUsing(fn (?string $state): array => json_decode($state ?: '[]', true))
                    ->columnSpanFull(),
            ]);
    }

    private static function validJsonRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            $decoded = is_string($value) ? json_decode($value, true) : null;

            if (! is_string($value) || json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                $fail('The :attribute field must contain a valid JSON object or array.');
            }
        };
    }
}
