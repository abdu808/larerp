<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BeneficiariesOperationsOverview;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class BeneficiariesOperationsDashboard extends Dashboard
{
    protected static string $routePath = 'beneficiaries-operations';

    protected static ?string $navigationLabel = 'تشغيل المستفيدين';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'لوحة تشغيل المستفيدين';

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            BeneficiariesOperationsOverview::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
