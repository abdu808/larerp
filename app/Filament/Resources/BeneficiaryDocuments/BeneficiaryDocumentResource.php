<?php

namespace App\Filament\Resources\BeneficiaryDocuments;

use App\Filament\Resources\BeneficiaryDocuments\Pages\CreateBeneficiaryDocument;
use App\Filament\Resources\BeneficiaryDocuments\Pages\EditBeneficiaryDocument;
use App\Filament\Resources\BeneficiaryDocuments\Pages\ListBeneficiaryDocuments;
use App\Filament\Resources\BeneficiaryDocuments\Schemas\BeneficiaryDocumentForm;
use App\Filament\Resources\BeneficiaryDocuments\Tables\BeneficiaryDocumentsTable;
use App\Models\BeneficiaryDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BeneficiaryDocumentResource extends Resource
{
    protected static ?string $model = BeneficiaryDocument::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'وثيقة مستفيد';

    protected static ?string $pluralModelLabel = 'وثائق المستفيدين';

    protected static ?string $navigationLabel = 'وثائق المستفيدين';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return BeneficiaryDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BeneficiaryDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeneficiaryDocuments::route('/'),
            'create' => CreateBeneficiaryDocument::route('/create'),
            'edit' => EditBeneficiaryDocument::route('/{record}/edit'),
        ];
    }
}
