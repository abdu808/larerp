<?php

namespace App\Filament\Resources\SupportPlans\Pages;

use App\Filament\Resources\SupportPlans\SupportPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupportPlan extends EditRecord
{
    protected static string $resource = SupportPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
