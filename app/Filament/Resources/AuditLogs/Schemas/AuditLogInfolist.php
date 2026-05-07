<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('-'),
                TextEntry::make('action')
                    ->label('الإجراء'),
                TextEntry::make('auditable_type')
                    ->label('نوع السجل')
                    ->placeholder('-'),
                TextEntry::make('auditable_id')
                    ->label('معرف السجل')
                    ->placeholder('-'),
                TextEntry::make('summary')
                    ->label('الملخص')
                    ->placeholder('-'),
                TextEntry::make('old_values')
                    ->label('القيم السابقة')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('new_values')
                    ->label('القيم الجديدة')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('عنوان IP')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label('المتصفح')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('تاريخ العملية')
                    ->dateTime(),
            ]);
    }
}
