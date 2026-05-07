<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الدور')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Hidden::make('guard_name')
                    ->label('الحارس')
                    ->required()
                    ->default('web'),
                Select::make('permissions')
                    ->label('الصلاحيات')
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
