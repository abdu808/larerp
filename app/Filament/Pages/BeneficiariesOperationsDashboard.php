<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BeneficiariesOperationsOverview;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use UnitEnum;

class BeneficiariesOperationsDashboard extends Dashboard
{
    protected static string $routePath = 'beneficiaries-operations';

    protected static ?string $navigationLabel = 'لوحة المستفيدين';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = '1. تشغيل القسم';

    protected static ?int $navigationSort = 10;

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
