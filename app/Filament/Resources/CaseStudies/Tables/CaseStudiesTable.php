<?php

namespace App\Filament\Resources\CaseStudies\Tables;

use App\Models\CaseStudy;
use App\Models\NeedsAssessment;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CaseStudiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('socialCase.case_number')->label('رقم الحالة')->searchable()->sortable(),
                TextColumn::make('researcher.name')->label('الباحث')->searchable()->sortable(),
                TextColumn::make('supervisor.name')->label('المشرف')->searchable()->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => CaseStudy::statusLabelFor($state))
                    ->color(fn (?string $state): string => CaseStudy::statusColorFor($state))
                    ->sortable(),
                TextColumn::make('needsAssessment.total_score')->label('درجة الاحتياج')->sortable()->placeholder('-'),
                TextColumn::make('needsAssessment.level')
                    ->label('مستوى الاحتياج')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === null ? '-' : NeedsAssessment::levelLabelFor($state))
                    ->color(fn (?string $state): string => NeedsAssessment::levelColorFor($state)),
                TextColumn::make('started_at')->label('تاريخ البدء')->date()->sortable(),
                TextColumn::make('ready_for_supervisor_at')->label('تاريخ الجاهزية')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completed_at')->label('تاريخ الإكمال')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(CaseStudy::STATUS_OPTIONS),
                SelectFilter::make('researcher_id')->label('الباحث')->relationship('researcher', 'name')->searchable()->preload(),
                SelectFilter::make('supervisor_id')->label('المشرف')->relationship('supervisor', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
