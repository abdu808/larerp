<?php

namespace App\Filament\Resources\FieldVisits\Pages;

use App\Filament\Resources\FieldVisits\FieldVisitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFieldVisit extends EditRecord
{
    protected static string $resource = FieldVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
