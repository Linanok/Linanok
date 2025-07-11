<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPanelAccessible
{
    public function handle(Request $request, Closure $next): Response
    {
        $currentDomain = current_domain();
        if (! isset($currentDomain)) {
            if (! Domain::exists()) {
                return $next($request);
            }
        } else {
            if ($currentDomain->is_admin_panel_active) {
                return $next($request);
            }
        }

        abort(Response::HTTP_NOT_FOUND);
    }
}
