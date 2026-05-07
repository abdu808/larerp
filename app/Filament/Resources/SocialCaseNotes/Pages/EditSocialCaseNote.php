<?php

namespace App\Filament\Resources\SocialCaseNotes\Pages;

use App\Filament\Resources\SocialCaseNotes\SocialCaseNoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialCaseNote extends EditRecord
{
    protected static string $resource = SocialCaseNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
