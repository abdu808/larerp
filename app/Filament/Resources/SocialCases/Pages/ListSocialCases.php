<?php

namespace App\Filament\Resources\SocialCases\Pages;

use App\Filament\Resources\SocialCases\SocialCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialCases extends ListRecords
{
    protected static string $resource = SocialCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
