<?php

namespace App\Filament\Widgets;

use App\Models\OrganizationProfile;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class FoundationStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'ملخص التأسيس';

    protected ?string $description = 'هذه الشاشة توضح ما تم تجهيزه فعليا في أساس النظام.';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('الجمعيات المعرفة', OrganizationProfile::query()->count())
                ->description('كيان مستقل لكل جمعية')
                ->descriptionIcon(Heroicon::BuildingOffice2)
                ->color('primary'),
            Stat::make('المستخدمون', User::query()->count())
                ->description('إدارة دخول وصلاحيات')
                ->descriptionIcon(Heroicon::UserGroup)
                ->color('info'),
            Stat::make('الأدوار', Role::query()->count())
                ->description('Super Admin و Admin كبداية')
                ->descriptionIcon(Heroicon::ShieldCheck)
                ->color('success'),
        ];
    }
}
