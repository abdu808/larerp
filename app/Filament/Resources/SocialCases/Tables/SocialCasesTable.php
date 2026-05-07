<?php

namespace App\Filament\Resources\SocialCases\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('type')->label('النوع')->badge(),
                TextColumn::make('status')->label('الحالة')->badge()->sortable(),
                TextColumn::make('priority')->label('الأولوية')->badge()->sortable(),
                TextColumn::make('opened_at')->label('تاريخ الفتح')->date()->sortable(),
                TextColumn::make('notes_count')->label('الملاحظات')->counts('notes')->sortable(),
                TextColumn::make('attachments_count')->label('المرفقات')->counts('attachments')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
