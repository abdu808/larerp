<?php

namespace App\Filament\Resources\NeedsAssessments\Pages;

use App\Filament\Resources\NeedsAssessments\NeedsAssessmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNeedsAssessment extends EditRecord
{
    protected static string $resource = NeedsAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
