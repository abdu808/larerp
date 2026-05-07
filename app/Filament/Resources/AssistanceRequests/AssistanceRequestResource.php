<?php

namespace App\Filament\Resources\AssistanceRequests;

use App\Filament\Resources\AssistanceRequests\Pages\CreateAssistanceRequest;
use App\Filament\Resources\AssistanceRequests\Pages\EditAssistanceRequest;
use App\Filament\Resources\AssistanceRequests\Pages\ListAssistanceRequests;
use App\Filament\Resources\AssistanceRequests\Schemas\AssistanceRequestForm;
use App\Filament\Resources\AssistanceRequests\Tables\AssistanceRequestsTable;
use App\Models\AssistanceRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssistanceRequestResource extends Resource
{
    protected static ?string $model = AssistanceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'طلب خدمة';

    protected static ?string $pluralModelLabel = 'طلبات الخدمة';

    protected static ?string $navigationLabel = 'طلبات الخدمة';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return AssistanceRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssistanceRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssistanceRequests::route('/'),
            'create' => CreateAssistanceRequest::route('/create'),
            'edit' => EditAssistanceRequest::route('/{record}/edit'),
        ];
    }
}
