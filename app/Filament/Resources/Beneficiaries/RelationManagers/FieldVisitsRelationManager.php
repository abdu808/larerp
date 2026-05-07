<?php

namespace App\Filament\Resources\Beneficiaries\RelationManagers;

use App\Models\FieldVisit;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FieldVisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'fieldVisits';

    protected static ?string $title = 'البحث الميداني';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')->label('الموعد')->dateTime()->sortable(),
                TextColumn::make('visitor.name')->label('الباحث')->searchable(),
                TextColumn::make('type')
                    ->label('نوع الزيارة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => FieldVisit::typeLabelFor($state)),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => FieldVisit::statusLabelFor($state))
                    ->color(fn (?string $state): string => FieldVisit::statusColorFor($state)),
                IconColumn::make('is_urgent')->label('عاجلة')->boolean(),
                TextColumn::make('building_status')->label('حالة المبنى')->toggleable(),
                TextColumn::make('furniture_status')->label('حالة الأثاث')->toggleable(),
                TextColumn::make('location')->label('الموقع')->searchable()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة زيارة')
                    ->mutateDataUsing(function (array $data): array {
                        $data['beneficiary_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->schema(self::visitSchema()),
            ])
            ->recordActions([
                EditAction::make()->schema(self::visitSchema()),
            ]);
    }

    /**
     * @return array<int, object>
     */
    private static function visitSchema(): array
    {
        return [
            Select::make('visitor_id')
                ->label('الباحث')
                ->relationship('visitor', 'name')
                ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                ->searchable()
                ->preload(),
            Select::make('status')->label('الحالة')->required()->default(FieldVisit::STATUS_SCHEDULED)->options(FieldVisit::STATUS_OPTIONS),
            Select::make('type')->label('نوع الزيارة')->required()->default(FieldVisit::TYPE_FIELD)->options(FieldVisit::TYPE_OPTIONS),
            Toggle::make('is_urgent')->label('حالة عاجلة/خطرة')->default(false),
            DateTimePicker::make('scheduled_at')->label('موعد الزيارة'),
            DateTimePicker::make('completed_at')->label('وقت الإكمال'),
            TextInput::make('location')->label('الموقع/رابط الموقع')->maxLength(255)->columnSpanFull(),
            Select::make('building_status')
                ->label('حالة المبنى')
                ->options([
                    'excellent' => 'ممتاز',
                    'good' => 'جيد',
                    'average' => 'متوسط',
                    'poor' => 'سيئ',
                    'dilapidated' => 'متهالك',
                ]),
            Select::make('furniture_status')
                ->label('حالة الأثاث')
                ->options([
                    'good' => 'جيد',
                    'average' => 'متوسط',
                    'poor' => 'سيئ',
                    'need_replacement' => 'يحتاج استبدال',
                ]),
            Textarea::make('purpose')->label('الغرض من الزيارة')->columnSpanFull(),
            Textarea::make('findings')->label('النتائج والملاحظات')->columnSpanFull(),
            Textarea::make('recommendations')->label('توصيات الباحث')->columnSpanFull(),
            Textarea::make('next_action')->label('الإجراء التالي')->columnSpanFull(),
        ];
    }
}
