<?php

namespace App\Filament\Pages;

use App\Filament\Admin\Widgets\AddCurrentDomain;
use App\Filament\Admin\Widgets\MostVisitedLinks;
use App\Filament\Admin\Widgets\QuickLinkCreator;
use App\Filament\Admin\Widgets\RecentActivity;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\TopCountriesChart;
use App\Filament\Admin\Widgets\VisitsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AddCurrentDomain::class,
            QuickLinkCreator::class,
            StatsOverview::class,
            MostVisitedLinks::class,
            RecentActivity::class,
            TopCountriesChart::class,
            VisitsChart::class,
        ];
    }
}
