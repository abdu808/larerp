<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الصلاحية')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Hidden::make('guard_name')
                    ->label('الحارس')
                    ->required()
                    ->default('web'),
            ]);
    }
}
