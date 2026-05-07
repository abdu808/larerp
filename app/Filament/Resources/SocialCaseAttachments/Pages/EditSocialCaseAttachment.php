<?php

namespace App\Filament\Resources\SocialCaseAttachments\Pages;

use App\Filament\Resources\SocialCaseAttachments\SocialCaseAttachmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialCaseAttachment extends EditRecord
{
    protected static string $resource = SocialCaseAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
