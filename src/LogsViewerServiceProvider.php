<?php

namespace Tong\LaravelLogsViewer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LogsViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/logs-viewer.php', 'logs-viewer');
    }

    public function boot(): void
    {
        // Publish config and views
        $this->publishes([
            __DIR__.'/../config/logs-viewer.php' => config_path('logs-viewer.php'),
        ], 'logs-viewer-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'logs-viewer');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/logs-viewer'),
        ], 'logs-viewer-views');

        // Routes
        if (! $this->app->routesAreCached()) {
            Route::middleware(config('logs-viewer.middleware', ['web']))
                ->prefix(config('logs-viewer.route_prefix', 'logs-viewer'))
                ->group(__DIR__.'/routes.php');
        }
    }
}


