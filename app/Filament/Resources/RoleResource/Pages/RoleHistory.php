<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\History\RecordHistory;

class RoleHistory extends RecordHistory
{
    protected static string $resource = RoleResource::class;
}
