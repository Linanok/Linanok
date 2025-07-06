<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLink extends EditRecord
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LinkResource\Widgets\LinkVisitsCountChart::class,
            LinkResource\Widgets\LinkVisitsByBrowserPieChart::class,
            LinkResource\Widgets\LinkVisitsByPlatformPieChart::class,
            LinkResource\Widgets\LinkVisitsByCountryPieChart::class,
        ];
    }
}
