<?php

namespace App\Filament\Resources\BeneficiaryDocuments\Pages;

use App\Filament\Resources\BeneficiaryDocuments\BeneficiaryDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaryDocuments extends ListRecords
{
    protected static string $resource = BeneficiaryDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
