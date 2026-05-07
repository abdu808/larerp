<?php

namespace App\Filament\Resources\SocialCaseNotes;

use App\Filament\Resources\SocialCaseNotes\Pages\CreateSocialCaseNote;
use App\Filament\Resources\SocialCaseNotes\Pages\EditSocialCaseNote;
use App\Filament\Resources\SocialCaseNotes\Pages\ListSocialCaseNotes;
use App\Filament\Resources\SocialCaseNotes\Schemas\SocialCaseNoteForm;
use App\Filament\Resources\SocialCaseNotes\Tables\SocialCaseNotesTable;
use App\Models\SocialCaseNote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SocialCaseNoteResource extends Resource
{
    protected static ?string $model = SocialCaseNote::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'ملاحظة حالة';

    protected static ?string $pluralModelLabel = 'ملاحظات الحالات';

    protected static ?string $navigationLabel = 'ملاحظات الحالات';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return SocialCaseNoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialCaseNotesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocialCaseNotes::route('/'),
            'create' => CreateSocialCaseNote::route('/create'),
            'edit' => EditSocialCaseNote::route('/{record}/edit'),
        ];
    }
}
