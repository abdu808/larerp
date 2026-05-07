<?php

namespace App\Filament\Resources\SocialCaseAttachments;

use App\Filament\Resources\SocialCaseAttachments\Pages\CreateSocialCaseAttachment;
use App\Filament\Resources\SocialCaseAttachments\Pages\EditSocialCaseAttachment;
use App\Filament\Resources\SocialCaseAttachments\Pages\ListSocialCaseAttachments;
use App\Filament\Resources\SocialCaseAttachments\Schemas\SocialCaseAttachmentForm;
use App\Filament\Resources\SocialCaseAttachments\Tables\SocialCaseAttachmentsTable;
use App\Models\SocialCaseAttachment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SocialCaseAttachmentResource extends Resource
{
    protected static ?string $model = SocialCaseAttachment::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'مرفق حالة';

    protected static ?string $pluralModelLabel = 'مرفقات الحالات';

    protected static ?string $navigationLabel = 'مرفقات الحالات';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return SocialCaseAttachmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialCaseAttachmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocialCaseAttachments::route('/'),
            'create' => CreateSocialCaseAttachment::route('/create'),
            'edit' => EditSocialCaseAttachment::route('/{record}/edit'),
        ];
    }
}
