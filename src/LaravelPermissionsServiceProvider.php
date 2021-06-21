<?php

namespace Metrix\LaravelPermissions;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Metrix\LaravelPermissions\Console\ClearPermissionCache;
use Metrix\LaravelPermissions\Console\ManagePermissions;
use Metrix\LaravelPermissions\Console\ManageRoles;

/**
 *  Laravel Permissions Service Provider
 */
class LaravelPermissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \Metrix\LaravelPermissions\Acl $acl
     */
    public function boot(Acl $acl)
    {
        $this->app['events']->listen(Authenticated::class, function ($event) use ($acl) {
            $acl->boot(Auth::id());
        });

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('permissions.php'),
            ], 'permissions');

            // Registering package commands.
            $this->commands([
                ClearPermissionCache::class,
                ManagePermissions::class,
                ManageRoles::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'permissions');

        // Register the main class to use with the facade
        $this->app->singleton(Acl::class, function () {
            return new Acl();
        });
    }

    /**
     * What we are providing.
     *
     * @return array
     */
    public function provides(): array
    {
        return [Acl::class];
    }
}
