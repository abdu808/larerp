<?php

namespace App\Filament\Resources\BeneficiaryDocuments\Tables;

use App\Models\BeneficiaryDocument;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BeneficiaryDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                TextColumn::make('beneficiary.full_name')->label('المستفيد')->placeholder('-'),
                TextColumn::make('family.name')->label('ملف المستفيد')->searchable()->placeholder('-'),
                TextColumn::make('document_type')->label('نوع الوثيقة')->badge()->formatStateUsing(fn (?string $state): string => BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('sensitivity_level')
                    ->label('السرية')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => BeneficiaryDocument::sensitivityLevelLabelFor($state))
                    ->color(fn (?string $state): string => BeneficiaryDocument::sensitivityLevelColorFor($state)),
                TextColumn::make('verification_status')
                    ->label('التحقق')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => BeneficiaryDocument::verificationStatusLabelFor($state))
                    ->color(fn (?string $state): string => BeneficiaryDocument::verificationStatusColorFor($state))
                    ->sortable(),
                TextColumn::make('expires_on')->label('ينتهي في')->date()->sortable(),
                IconColumn::make('used_in_decision_at')->label('مستخدم في قرار')->boolean(),
                TextColumn::make('verifiedBy.name')->label('تحقق بواسطة')->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('تاريخ الرفع')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('document_type')->label('نوع الوثيقة')->options(BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS),
                SelectFilter::make('sensitivity_level')->label('السرية')->options(BeneficiaryDocument::SENSITIVITY_LEVEL_OPTIONS),
                SelectFilter::make('verification_status')->label('حالة التحقق')->options(BeneficiaryDocument::VERIFICATION_STATUS_OPTIONS),
                SelectFilter::make('family_id')->label('ملف المستفيد')->relationship('family', 'name')->searchable()->preload(),
                Filter::make('expired')
                    ->label('منتهية')
                    ->query(fn (Builder $query): Builder => $query->whereDate('expires_on', '<', now()->toDateString())),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (BeneficiaryDocument $record): bool => ! $record->canBeDeleted()),
            ]);
    }
}
