<?php

namespace App\Filament\Resources\SupportPlans;

use App\Filament\Resources\SupportPlans\Pages\CreateSupportPlan;
use App\Filament\Resources\SupportPlans\Pages\EditSupportPlan;
use App\Filament\Resources\SupportPlans\Pages\ListSupportPlans;
use App\Models\SupportPlan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SupportPlanResource extends Resource
{
    protected static ?string $model = SupportPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'خطة دعم';

    protected static ?string $pluralModelLabel = 'خطط الدعم';

    protected static ?string $navigationLabel = 'خطط الدعم';

    protected static string|UnitEnum|null $navigationGroup = '5. التنفيذ والمتابعة';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('social_case_id')->label('الحالة الاجتماعية')->relationship('socialCase', 'case_number')->searchable()->preload()->required(),
            Select::make('plan_type')->label('نوع الخطة')->options(SupportPlan::TYPE_OPTIONS)->default(SupportPlan::TYPE_RELIEF)->required(),
            Select::make('status')->label('الحالة')->options(SupportPlan::STATUS_OPTIONS)->default(SupportPlan::STATUS_DRAFT)->required(),
            Select::make('owner_id')->label('مالك الخطة')->relationship('owner', 'name')->searchable()->preload(),
            DatePicker::make('start_date')->label('تاريخ البداية'),
            DatePicker::make('end_date')->label('تاريخ النهاية'),
            Textarea::make('goal')->label('الهدف')->required()->columnSpanFull(),
            Textarea::make('success_criteria')->label('معايير النجاح')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('plan_type')->label('النوع')->badge()->formatStateUsing(fn (?string $state): string => SupportPlan::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SupportPlan::statusLabelFor($state))
                    ->color(fn (?string $state): string => SupportPlan::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('owner.name')->label('المالك')->placeholder('-'),
                TextColumn::make('start_date')->label('البداية')->date()->sortable(),
                TextColumn::make('end_date')->label('النهاية')->date()->sortable(),
                TextColumn::make('service_deliveries_count')->label('التنفيذات')->counts('serviceDeliveries')->sortable(),
                TextColumn::make('follow_ups_count')->label('المتابعات')->counts('followUps')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(SupportPlan::STATUS_OPTIONS),
                SelectFilter::make('plan_type')->label('نوع الخطة')->options(SupportPlan::TYPE_OPTIONS),
                SelectFilter::make('social_case_id')->label('الحالة')->relationship('socialCase', 'case_number')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportPlans::route('/'),
            'create' => CreateSupportPlan::route('/create'),
            'edit' => EditSupportPlan::route('/{record}/edit'),
        ];
    }
}
