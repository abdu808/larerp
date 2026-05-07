<?php

namespace App\Filament\Resources\ServiceDeliveries;

use App\Filament\Resources\ServiceDeliveries\Pages\CreateServiceDelivery;
use App\Filament\Resources\ServiceDeliveries\Pages\EditServiceDelivery;
use App\Filament\Resources\ServiceDeliveries\Pages\ListServiceDeliveries;
use App\Models\ServiceDelivery;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ServiceDeliveryResource extends Resource
{
    protected static ?string $model = ServiceDelivery::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $modelLabel = 'تنفيذ خدمة';

    protected static ?string $pluralModelLabel = 'تنفيذ الخدمات';

    protected static ?string $navigationLabel = 'تنفيذ الخدمات';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 33;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('social_case_id')->label('الحالة الاجتماعية')->relationship('socialCase', 'case_number')->searchable()->preload()->required(),
            Select::make('support_plan_id')->label('خطة الدعم')->relationship('supportPlan', 'goal')->searchable()->preload(),
            Select::make('committee_decision_id')->label('قرار اللجنة')->relationship('committeeDecision', 'id')->searchable()->preload(),
            Select::make('delivery_type')->label('نوع التنفيذ')->options(ServiceDelivery::TYPE_OPTIONS)->required(),
            Select::make('status')->label('الحالة')->options(ServiceDelivery::STATUS_OPTIONS)->default(ServiceDelivery::STATUS_SCHEDULED)->required(),
            TextInput::make('amount')->label('المبلغ')->numeric()->minValue(0.01),
            TextInput::make('quantity')->label('الكمية')->numeric()->minValue(0.01),
            TextInput::make('unit')->label('الوحدة')->maxLength(255),
            DatePicker::make('delivered_at')->label('تاريخ التنفيذ'),
            Select::make('delivered_by_id')->label('منفذ الخدمة')->relationship('deliveredBy', 'name')->searchable()->preload(),
            Textarea::make('service_description')->label('وصف الخدمة')->required()->columnSpanFull(),
            Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('delivery_type')->label('النوع')->badge()->formatStateUsing(fn (?string $state): string => ServiceDelivery::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ServiceDelivery::statusLabelFor($state))
                    ->color(fn (?string $state): string => ServiceDelivery::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('amount')->label('المبلغ')->money('SAR')->placeholder('-')->sortable(),
                TextColumn::make('quantity')->label('الكمية')->placeholder('-')->sortable(),
                TextColumn::make('unit')->label('الوحدة')->placeholder('-'),
                TextColumn::make('delivered_at')->label('تاريخ التنفيذ')->date()->sortable(),
                TextColumn::make('deliveredBy.name')->label('المنفذ')->placeholder('-'),
                TextColumn::make('follow_ups_count')->label('المتابعات')->counts('followUps')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(ServiceDelivery::STATUS_OPTIONS),
                SelectFilter::make('delivery_type')->label('نوع التنفيذ')->options(ServiceDelivery::TYPE_OPTIONS),
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
            'index' => ListServiceDeliveries::route('/'),
            'create' => CreateServiceDelivery::route('/create'),
            'edit' => EditServiceDelivery::route('/{record}/edit'),
        ];
    }
}
