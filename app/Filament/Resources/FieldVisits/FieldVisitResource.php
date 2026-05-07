<?php

namespace App\Filament\Resources\FieldVisits;

use App\Filament\Resources\FieldVisits\Pages\CreateFieldVisit;
use App\Filament\Resources\FieldVisits\Pages\EditFieldVisit;
use App\Filament\Resources\FieldVisits\Pages\ListFieldVisits;
use App\Filament\Resources\FieldVisits\Schemas\FieldVisitForm;
use App\Filament\Resources\FieldVisits\Tables\FieldVisitsTable;
use App\Models\FieldVisit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FieldVisitResource extends Resource
{
    protected static ?string $model = FieldVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $modelLabel = 'زيارة';

    protected static ?string $pluralModelLabel = 'الزيارات';

    protected static ?string $navigationLabel = 'الزيارات الميدانية';

    protected static string|UnitEnum|null $navigationGroup = '4. الدراسة والاعتماد';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return FieldVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldVisitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFieldVisits::route('/'),
            'create' => CreateFieldVisit::route('/create'),
            'edit' => EditFieldVisit::route('/{record}/edit'),
        ];
    }
}
