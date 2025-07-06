<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\History\RecordHistory;

class DomainHistory extends RecordHistory
{
    protected static string $resource = DomainResource::class;
}
