<?php

namespace Vis\Translations;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__.'/../vendor/autoload.php';
        require __DIR__.'/Http/helpers.php';

        $this->setupRoutes($this->app->router);
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'translations');

        $this->publishes([
            __DIR__
            .'/published'     => public_path('packages/vis/translations'),
        ], 'translations');

        $this->publishes([
            __DIR__
            .'/published' => public_path('packages/vis/translations'),
        ], 'public');

        $this->publishes([
            realpath(__DIR__.'/Migrations') => $this->app->databasePath().'/migrations',
        ]);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/Http/routers.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.translations.generate', function ($app) {
            return new GenerateTranslate();
        });

        $this->app->singleton('command.translations.tables', function ($app) {
            return new GenerateTranslateTable();
        });

        $this->commands('command.translations.generate');
        $this->commands('command.translations.tables');

        \App::singleton('arrayTranslate', function () {
            return Trans::fillCacheTrans();
        });
    }
}
