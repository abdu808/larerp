<?php

namespace App\Filament\Resources\ServiceDeliveries\Pages;

use App\Filament\Resources\ServiceDeliveries\ServiceDeliveryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceDelivery extends EditRecord
{
    protected static string $resource = ServiceDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
