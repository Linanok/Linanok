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
        $middleware->trustProxies(at: explode(',', env('TRUSTED_PROXIES') ?? ''));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
