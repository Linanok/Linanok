<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\LinkResource;
use App\History\RecordHistory;

class TagHistory extends RecordHistory
{
    protected static string $resource = LinkResource::class;
}
