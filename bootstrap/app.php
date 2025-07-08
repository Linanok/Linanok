<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $trustedProxies = env('TRUSTED_PROXIES', '');
        if ($trustedProxies === '*') {
            $middleware->trustProxies(at: '*');
        } else {
            $middleware->trustProxies(at: explode(',', $trustedProxies));
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
