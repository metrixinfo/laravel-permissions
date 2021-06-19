<?php

namespace Metrix\LaravelPermissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelPermissionsServiceProvider
 *
 * @package Metrix\LaravelPermissions
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
        $acl->boot(Auth::id());

        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-permissions');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-permissions');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-permissions.php'),
            ], 'config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-permissions'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-permissions'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-permissions'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        // $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-permissions');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-permissions', function () {
            return new Acl();
        });
    }
}
