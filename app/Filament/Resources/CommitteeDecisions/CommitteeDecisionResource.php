<?php

namespace App\Filament\Resources\CommitteeDecisions;

use App\Filament\Resources\CommitteeDecisions\Pages\CreateCommitteeDecision;
use App\Filament\Resources\CommitteeDecisions\Pages\EditCommitteeDecision;
use App\Filament\Resources\CommitteeDecisions\Pages\ListCommitteeDecisions;
use App\Models\CommitteeDecision;
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

class CommitteeDecisionResource extends Resource
{
    protected static ?string $model = CommitteeDecision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $modelLabel = 'قرار لجنة';

    protected static ?string $pluralModelLabel = 'قرارات اللجنة';

    protected static ?string $navigationLabel = 'قرارات اللجنة';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 31;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('social_case_id')
                ->label('الحالة الاجتماعية')
                ->relationship('socialCase', 'case_number')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('assistance_request_id')
                ->label('طلب الخدمة')
                ->relationship('assistanceRequest', 'request_number')
                ->searchable()
                ->preload(),
            Select::make('decision_type')->label('نوع القرار')->options(CommitteeDecision::TYPE_OPTIONS)->required(),
            Select::make('status')->label('الحالة')->options(CommitteeDecision::STATUS_OPTIONS)->default(CommitteeDecision::STATUS_DRAFT)->required(),
            TextInput::make('approved_amount')->label('المبلغ المعتمد')->numeric()->minValue(0),
            TextInput::make('approved_service_type')->label('نوع الخدمة المعتمدة')->maxLength(255),
            DatePicker::make('effective_from')->label('يسري من'),
            DatePicker::make('effective_to')->label('يسري إلى'),
            Select::make('decided_by_id')->label('صاحب القرار')->relationship('decidedBy', 'name')->searchable()->preload(),
            Textarea::make('reason')->label('سبب القرار')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('assistanceRequest.request_number')->label('طلب الخدمة')->placeholder('-')->searchable(),
                TextColumn::make('decision_type')->label('نوع القرار')->badge()->formatStateUsing(fn (?string $state): string => CommitteeDecision::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => CommitteeDecision::statusLabelFor($state))
                    ->color(fn (?string $state): string => CommitteeDecision::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('approved_amount')->label('المبلغ')->money('SAR')->placeholder('-')->sortable(),
                TextColumn::make('approved_service_type')->label('الخدمة')->placeholder('-')->searchable(),
                TextColumn::make('effective_from')->label('من')->date()->sortable(),
                TextColumn::make('effective_to')->label('إلى')->date()->sortable(),
                TextColumn::make('decidedBy.name')->label('صاحب القرار')->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(CommitteeDecision::STATUS_OPTIONS),
                SelectFilter::make('decision_type')->label('نوع القرار')->options(CommitteeDecision::TYPE_OPTIONS),
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
            'index' => ListCommitteeDecisions::route('/'),
            'create' => CreateCommitteeDecision::route('/create'),
            'edit' => EditCommitteeDecision::route('/{record}/edit'),
        ];
    }
}
