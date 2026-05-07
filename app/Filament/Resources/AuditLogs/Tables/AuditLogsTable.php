<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('الإجراء')
                    ->searchable(),
                TextColumn::make('auditable_type')
                    ->label('نوع السجل')
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('معرف السجل')
                    ->searchable(),
                TextColumn::make('summary')
                    ->label('الملخص')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('عنوان IP')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('تاريخ العملية')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
