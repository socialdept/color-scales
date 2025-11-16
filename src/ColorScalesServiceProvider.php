<?php

namespace SocialDept\ColorScales;

use Illuminate\Support\ServiceProvider;

class ColorScalesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'socialdept');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'socialdept');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/color-scales.php', 'color-scales');

        // Register the service the package provides.
        $this->app->singleton('color-scales', function ($app) {
            return new ColorScales;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['color-scales'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/color-scales.php' => config_path('color-scales.php'),
        ], 'color-scales.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/socialdept'),
        ], 'color-scales.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/socialdept'),
        ], 'color-scales.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/socialdept'),
        ], 'color-scales.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
