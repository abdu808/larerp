<?php

namespace App\Filament\Resources\FieldVisits\Pages;

use App\Filament\Resources\FieldVisits\FieldVisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFieldVisits extends ListRecords
{
    protected static string $resource = FieldVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
