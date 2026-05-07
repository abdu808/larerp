<?php

namespace App\Filament\Resources\NeedsAssessments\Pages;

use App\Filament\Resources\NeedsAssessments\NeedsAssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNeedsAssessments extends ListRecords
{
    protected static string $resource = NeedsAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
