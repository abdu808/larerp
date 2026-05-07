<?php

namespace App\Filament\Resources\BeneficiaryDocuments\Pages;

use App\Filament\Resources\BeneficiaryDocuments\BeneficiaryDocumentResource;
use App\Models\BeneficiaryDocument;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBeneficiaryDocument extends EditRecord
{
    protected static string $resource = BeneficiaryDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->hidden(fn (BeneficiaryDocument $record): bool => ! $record->canBeDeleted()),
        ];
    }
}
