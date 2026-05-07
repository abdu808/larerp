<?php

namespace App\Filament\Resources\Beneficiaries\Pages;

use App\Filament\Resources\Beneficiaries\BeneficiaryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiary extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'ملف المستفيد';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.beneficiaries.pages.beneficiary-360')
                    ->viewData(fn (): array => [
                        'record' => $this->getRecord()->loadMissing([
                            'dependents',
                            'socialCases',
                            'documents',
                            'assistanceRequests',
                            'fieldVisits',
                        ]),
                        'editUrl' => static::getResource()::getUrl('edit', ['record' => $this->getRecord()], panel: 'admin'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('تعديل الملف'),
        ];
    }
}
