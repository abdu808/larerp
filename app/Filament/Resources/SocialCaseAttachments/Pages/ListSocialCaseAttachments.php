<?php

namespace App\Filament\Resources\SocialCaseAttachments\Pages;

use App\Filament\Resources\SocialCaseAttachments\SocialCaseAttachmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialCaseAttachments extends ListRecords
{
    protected static string $resource = SocialCaseAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
