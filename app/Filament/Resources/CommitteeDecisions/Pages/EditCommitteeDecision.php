<?php

namespace App\Filament\Resources\CommitteeDecisions\Pages;

use App\Filament\Resources\CommitteeDecisions\CommitteeDecisionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommitteeDecision extends EditRecord
{
    protected static string $resource = CommitteeDecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
