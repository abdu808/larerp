<?php

namespace App\Filament\Resources\SocialCaseNotes\Pages;

use App\Filament\Resources\SocialCaseNotes\SocialCaseNoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialCaseNotes extends ListRecords
{
    protected static string $resource = SocialCaseNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
