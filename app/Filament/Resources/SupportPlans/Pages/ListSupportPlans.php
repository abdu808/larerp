<?php

namespace App\Filament\Resources\SupportPlans\Pages;

use App\Filament\Resources\SupportPlans\SupportPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupportPlans extends ListRecords
{
    protected static string $resource = SupportPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
