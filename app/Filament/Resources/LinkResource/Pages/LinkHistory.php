<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use App\History\RecordHistory;

class LinkHistory extends RecordHistory
{
    protected static string $resource = LinkResource::class;
}
