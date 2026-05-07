<?php

namespace App\Filament\Resources\SocialCases;

use App\Filament\Resources\SocialCases\Pages\CreateSocialCase;
use App\Filament\Resources\SocialCases\Pages\EditSocialCase;
use App\Filament\Resources\SocialCases\Pages\ListSocialCases;
use App\Filament\Resources\SocialCases\Schemas\SocialCaseForm;
use App\Filament\Resources\SocialCases\Tables\SocialCasesTable;
use App\Models\SocialCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SocialCaseResource extends Resource
{
    protected static ?string $model = SocialCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $modelLabel = 'حالة اجتماعية';

    protected static ?string $pluralModelLabel = 'الحالات الاجتماعية';

    protected static ?string $navigationLabel = 'ملفات الحالات';

    protected static string|UnitEnum|null $navigationGroup = '3. ملفات المستفيدين';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return SocialCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialCasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocialCases::route('/'),
            'create' => CreateSocialCase::route('/create'),
            'edit' => EditSocialCase::route('/{record}/edit'),
        ];
    }
}
