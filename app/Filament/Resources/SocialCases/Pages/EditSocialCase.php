<?php

namespace App\Filament\Resources\SocialCases\Pages;

use App\Filament\Resources\SocialCases\SocialCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialCase extends EditRecord
{
    protected static string $resource = SocialCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
