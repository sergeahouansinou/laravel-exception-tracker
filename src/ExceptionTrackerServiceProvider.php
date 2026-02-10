<?php

namespace ExceptionTracker;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use ExceptionTracker\Commands\ClearOldExceptions;

class ExceptionTrackerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/exception-tracker.php', 'exception-tracker');
    }

    public function boot(): void
    {
        $this->registerPublishables();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerRoutes();
        $this->registerCommands();
        $this->registerExceptionHandler();
    }

    protected function registerPublishables(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/exception-tracker.php' => config_path('exception-tracker.php'),
        ], 'exception-tracker-config');

        $this->publishes([
            __DIR__ . '/../resources/views/exception-tracker' => resource_path('views/vendor/exception-tracker'),
        ], 'exception-tracker-views');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views/exception-tracker',
            'exception-tracker'
        );
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ClearOldExceptions::class]);
        }
    }

    protected function registerExceptionHandler(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        if (method_exists($handler, 'reportable')) {
            $handler->reportable(function (\Throwable $e) {
                ExceptionTracker::handle($e);
            })->stop(false);
        }
    }
}
