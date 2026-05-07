<?php

namespace App\Filament\Resources\SocialCases\Tables;

use App\Models\SocialCase;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SocialCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('family.name')->label('العائلة')->searchable(),
                TextColumn::make('beneficiary.full_name')->label('المستفيد')->placeholder('-'),
                TextColumn::make('type')->label('النوع')->badge()->formatStateUsing(fn (?string $state): string => SocialCase::TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SocialCase::statusLabelFor($state))
                    ->color(fn (?string $state): string => SocialCase::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('الأولوية')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SocialCase::priorityLabelFor($state))
                    ->color(fn (?string $state): string => SocialCase::priorityColorFor($state))
                    ->sortable(),
                TextColumn::make('opened_at')->label('تاريخ الفتح')->date()->sortable(),
                TextColumn::make('closed_at')->label('تاريخ الإغلاق')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes_count')->label('الملاحظات')->counts('notes')->sortable(),
                TextColumn::make('attachments_count')->label('المرفقات')->counts('attachments')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(SocialCase::STATUS_OPTIONS),
                SelectFilter::make('priority')->label('الأولوية')->options(SocialCase::PRIORITY_OPTIONS),
                SelectFilter::make('type')->label('النوع')->options(SocialCase::TYPE_OPTIONS),
                SelectFilter::make('family_id')->label('العائلة')->relationship('family', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (SocialCase $record): bool => ! $record->canBeDeleted()),
            ]);
    }
}
