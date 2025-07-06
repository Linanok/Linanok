<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\History\RecordHistory;

class UserHistory extends RecordHistory
{
    protected static string $resource = UserResource::class;
}
