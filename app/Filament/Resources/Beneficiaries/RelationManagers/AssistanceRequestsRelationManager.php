<?php

namespace App\Filament\Resources\Beneficiaries\RelationManagers;

use App\Models\AssistanceRequest;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssistanceRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'assistanceRequests';

    protected static ?string $title = 'طلبات الخدمة';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')->label('رقم الطلب')->searchable()->sortable(),
                TextColumn::make('request_type')->label('نوع الطلب')->badge()->formatStateUsing(fn (?string $state): string => AssistanceRequest::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => AssistanceRequest::statusLabelFor($state))
                    ->color(fn (?string $state): string => AssistanceRequest::statusColorFor($state)),
                TextColumn::make('urgency')
                    ->label('الأولوية')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => AssistanceRequest::urgencyLabelFor($state))
                    ->color(fn (?string $state): string => AssistanceRequest::urgencyColorFor($state)),
                TextColumn::make('submitted_at')->label('تاريخ التقديم')->dateTime()->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة طلب')
                    ->mutateDataUsing(function (array $data): array {
                        $data['beneficiary_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->schema([
                        TextInput::make('request_number')->label('رقم الطلب')->required()->maxLength(255)->unique(AssistanceRequest::class, 'request_number'),
                        Select::make('request_type')->label('نوع الطلب')->required()->options(AssistanceRequest::TYPE_OPTIONS)->searchable(),
                        Select::make('status')->label('الحالة')->required()->default(AssistanceRequest::STATUS_SUBMITTED)->options(AssistanceRequest::STATUS_OPTIONS),
                        Select::make('urgency')->label('الأولوية')->required()->default('normal')->options(AssistanceRequest::URGENCY_OPTIONS),
                        TextInput::make('source')->label('مصدر الطلب')->maxLength(255),
                        DateTimePicker::make('submitted_at')->label('تاريخ التقديم'),
                        Textarea::make('description')->label('وصف الطلب')->required()->columnSpanFull(),
                        Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
