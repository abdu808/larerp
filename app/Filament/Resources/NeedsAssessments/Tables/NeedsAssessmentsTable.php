<?php

namespace App\Filament\Resources\NeedsAssessments\Tables;

use App\Models\NeedsAssessment;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NeedsAssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('caseStudy.socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('assessedBy.name')->label('المقيّم')->searchable()->sortable(),
                TextColumn::make('total_score')->label('المجموع')->sortable(),
                TextColumn::make('level')
                    ->label('المستوى')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => NeedsAssessment::levelLabelFor($state))
                    ->color(fn (?string $state): string => NeedsAssessment::levelColorFor($state))
                    ->sortable(),
                TextColumn::make('assessed_at')->label('وقت التقييم')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('آخر تحديث')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')->label('المستوى')->options(NeedsAssessment::LEVEL_OPTIONS),
                SelectFilter::make('assessed_by_id')->label('المقيّم')->relationship('assessedBy', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
