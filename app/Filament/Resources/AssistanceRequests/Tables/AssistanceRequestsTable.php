<?php

namespace App\Filament\Resources\AssistanceRequests\Tables;

use App\Models\AssistanceRequest;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssistanceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')->label('رقم الطلب')->searchable()->sortable(),
                TextColumn::make('family.name')->label('ملف المستفيد')->searchable()->placeholder('-'),
                TextColumn::make('beneficiary.full_name')->label('المستفيد')->placeholder('-'),
                TextColumn::make('request_type')->label('نوع الطلب')->badge()->formatStateUsing(fn (?string $state): string => AssistanceRequest::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => AssistanceRequest::statusLabelFor($state))
                    ->color(fn (?string $state): string => AssistanceRequest::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('urgency')
                    ->label('العجلة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => AssistanceRequest::urgencyLabelFor($state))
                    ->color(fn (?string $state): string => AssistanceRequest::urgencyColorFor($state))
                    ->sortable(),
                IconColumn::make('consent_to_store_data')->label('الموافقة')->boolean(),
                TextColumn::make('assignedTo.name')->label('المحال إليه')->placeholder('-')->toggleable(),
                TextColumn::make('submitted_at')->label('تاريخ التقديم')->dateTime()->sortable()->toggleable(),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(AssistanceRequest::STATUS_OPTIONS),
                SelectFilter::make('urgency')->label('العجلة')->options(AssistanceRequest::URGENCY_OPTIONS),
                SelectFilter::make('request_type')->label('نوع الطلب')->options(AssistanceRequest::TYPE_OPTIONS),
                SelectFilter::make('family_id')->label('ملف المستفيد')->relationship('family', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
