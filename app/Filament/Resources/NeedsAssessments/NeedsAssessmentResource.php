<?php

namespace App\Filament\Resources\NeedsAssessments;

use App\Filament\Resources\NeedsAssessments\Pages\CreateNeedsAssessment;
use App\Filament\Resources\NeedsAssessments\Pages\EditNeedsAssessment;
use App\Filament\Resources\NeedsAssessments\Pages\ListNeedsAssessments;
use App\Filament\Resources\NeedsAssessments\Schemas\NeedsAssessmentForm;
use App\Filament\Resources\NeedsAssessments\Tables\NeedsAssessmentsTable;
use App\Models\NeedsAssessment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NeedsAssessmentResource extends Resource
{
    protected static ?string $model = NeedsAssessment::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $modelLabel = 'تقييم احتياج';

    protected static ?string $pluralModelLabel = 'تقييمات الاحتياج';

    protected static ?string $navigationLabel = 'تقييمات الاحتياج';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 31;

    public static function form(Schema $schema): Schema
    {
        return NeedsAssessmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NeedsAssessmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNeedsAssessments::route('/'),
            'create' => CreateNeedsAssessment::route('/create'),
            'edit' => EditNeedsAssessment::route('/{record}/edit'),
        ];
    }
}
