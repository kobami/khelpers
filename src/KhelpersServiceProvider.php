<?php

namespace Kobami\Khelpers;

use Illuminate\Support\ServiceProvider;

class KhelpersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'khelpers');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'khelpers');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../config/config.php' => config_path('khelpers.php'),
                    __DIR__ . '/../config/wp-config-example.php' => base_path('wp-config-example.php'),
                    __DIR__ . '/../config/env-example' => base_path('env-example'),
                    __DIR__ . '/../config/wp-sqlite-db.php' => base_path('public/wp-content/db.php'),
                ],
                'config'
            );

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/khelpers'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/khelpers'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/khelpers'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands(
                [
                    Console\Commands\ImportDb::class,
                    Console\Commands\ResetPasswords::class,
                    Console\Commands\WPRenameSite::class,
                    Console\Commands\Serve::class,
                    Console\Commands\DownloadWordPress::class,
                ]
            );
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'khelpers');

        // Register the main class to use with the facade
        $this->app->singleton(
            'khelpers',
            function () {
                return new Khelpers;
            }
        );
    }
}
