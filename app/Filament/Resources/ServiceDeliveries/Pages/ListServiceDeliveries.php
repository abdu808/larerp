<?php

namespace App\Filament\Resources\ServiceDeliveries\Pages;

use App\Filament\Resources\ServiceDeliveries\ServiceDeliveryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceDeliveries extends ListRecords
{
    protected static string $resource = ServiceDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
