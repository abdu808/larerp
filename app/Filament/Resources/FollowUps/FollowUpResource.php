<?php

namespace App\Filament\Resources\FollowUps;

use App\Filament\Resources\FollowUps\Pages\CreateFollowUp;
use App\Filament\Resources\FollowUps\Pages\EditFollowUp;
use App\Filament\Resources\FollowUps\Pages\ListFollowUps;
use App\Models\FollowUp;
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

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'متابعة';

    protected static ?string $pluralModelLabel = 'المتابعات';

    protected static ?string $navigationLabel = 'المتابعات';

    protected static string|UnitEnum|null $navigationGroup = 'المستفيدون والملفات الاجتماعية';

    protected static ?int $navigationSort = 34;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('social_case_id')->label('الحالة الاجتماعية')->relationship('socialCase', 'case_number')->searchable()->preload()->required(),
            Select::make('support_plan_id')->label('خطة الدعم')->relationship('supportPlan', 'goal')->searchable()->preload(),
            Select::make('service_delivery_id')->label('تنفيذ الخدمة')->relationship('serviceDelivery', 'id')->searchable()->preload(),
            Select::make('status')->label('الحالة')->options(FollowUp::STATUS_OPTIONS)->default(FollowUp::STATUS_SCHEDULED)->required(),
            DatePicker::make('followed_up_at')->label('تاريخ المتابعة'),
            Select::make('improvement_level')->label('مستوى التحسن')->options(FollowUp::IMPROVEMENT_OPTIONS),
            Select::make('follow_up_decision')->label('قرار المتابعة')->options(FollowUp::DECISION_OPTIONS)->required(),
            DatePicker::make('next_follow_up_at')->label('الموعد القادم'),
            Select::make('followed_by_id')->label('المتابع')->relationship('followedBy', 'name')->searchable()->preload(),
            Textarea::make('outcome')->label('النتيجة')->required()->columnSpanFull(),
            Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => FollowUp::statusLabelFor($state))
                    ->color(fn (?string $state): string => FollowUp::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('improvement_level')->label('التحسن')->badge()->formatStateUsing(fn (?string $state): string => FollowUp::IMPROVEMENT_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('follow_up_decision')->label('قرار المتابعة')->badge()->formatStateUsing(fn (?string $state): string => FollowUp::DECISION_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('followed_up_at')->label('تاريخ المتابعة')->date()->sortable(),
                TextColumn::make('next_follow_up_at')->label('الموعد القادم')->date()->sortable(),
                TextColumn::make('followedBy.name')->label('المتابع')->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(FollowUp::STATUS_OPTIONS),
                SelectFilter::make('improvement_level')->label('مستوى التحسن')->options(FollowUp::IMPROVEMENT_OPTIONS),
                SelectFilter::make('follow_up_decision')->label('قرار المتابعة')->options(FollowUp::DECISION_OPTIONS),
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
            'index' => ListFollowUps::route('/'),
            'create' => CreateFollowUp::route('/create'),
            'edit' => EditFollowUp::route('/{record}/edit'),
        ];
    }
}
