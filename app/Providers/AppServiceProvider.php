<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Application Service Provider
 *
 * The main service provider for the application that handles:
 * - Conditional registration of development tools (Telescope)
 * - Application-wide service bindings and configurations
 * - Bootstrap logic that runs on every request
 *
 * @see \Laravel\Telescope\TelescopeServiceProvider
 * @see \App\Providers\TelescopeServiceProvider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Conditionally registers Laravel Telescope in local environment
     * for debugging and monitoring during development.
     */
    public function register(): void
    {
        // Only register Telescope in local environment and if the class exists
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
