<?php

namespace App\Filament\Resources\Beneficiaries\RelationManagers;

use App\Models\SocialCase;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialCasesRelationManager extends RelationManager
{
    protected static string $relationship = 'socialCases';

    protected static ?string $title = 'الحالات';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('type')->label('النوع')->badge()->formatStateUsing(fn (?string $state): string => SocialCase::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SocialCase::statusLabelFor($state))
                    ->color(fn (?string $state): string => SocialCase::statusColorFor($state)),
                TextColumn::make('priority')
                    ->label('الأولوية')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SocialCase::priorityLabelFor($state))
                    ->color(fn (?string $state): string => SocialCase::priorityColorFor($state)),
                TextColumn::make('opened_at')->label('تاريخ الفتح')->date(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة حالة')
                    ->mutateDataUsing(function (array $data): array {
                        $data['beneficiary_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->schema([
                        TextInput::make('case_number')->label('رقم الحالة')->required()->maxLength(255)->unique(SocialCase::class, 'case_number'),
                        Select::make('type')->label('نوع الحالة')->options(SocialCase::TYPE_OPTIONS)->searchable(),
                        Select::make('status')->label('الحالة')->required()->default(SocialCase::STATUS_OPEN)->options(SocialCase::STATUS_OPTIONS),
                        Select::make('priority')->label('الأولوية')->required()->default('normal')->options(SocialCase::PRIORITY_OPTIONS),
                        DatePicker::make('opened_at')->label('تاريخ الفتح'),
                        TextInput::make('monthly_income')->label('الدخل الشهري')->numeric()->minValue(0),
                        TextInput::make('monthly_expenses')->label('المصروفات الشهرية')->numeric()->minValue(0),
                        Textarea::make('summary')->label('ملخص الحالة')->required()->columnSpanFull(),
                        Textarea::make('needs')->label('الاحتياجات')->columnSpanFull(),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
