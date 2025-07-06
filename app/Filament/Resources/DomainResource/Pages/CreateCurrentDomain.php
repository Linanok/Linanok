<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Models\Domain;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrentDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    public function mount(): void
    {
        $request = request();
        $user = $request->user();
        if (! $user->can('create domain')) {
            abort(403);
        }

        $host = $request->getHttpHost();
        $protocol = $request->getScheme();
        $domain = Domain::firstOrCreate([
            'host' => $host,
            'protocol' => $protocol,
        ], [
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        redirect(route('filament.admin.resources.domains.edit', ['record' => $domain]), status: 301);
    }
}
