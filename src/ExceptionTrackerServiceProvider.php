<?php

namespace ExceptionTracker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use ExceptionTracker\Commands\ClearOldExceptions;

class ExceptionTrackerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/exception-tracker.php', 'exception-tracker');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/exception-tracker.php' => config_path('exception-tracker.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([ClearOldExceptions::class]);
        }

        // 🔥 Hook global sur les exceptions Laravel
        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {
            return new class($handler) implements ExceptionHandler {
                protected $handler;
                public function __construct($handler)
                {
                    $this->handler = $handler;
                }

                public function report(\Throwable $e)
                {
                    if (config('exception-tracker.enabled')) {
                        \ExceptionTracker\ExceptionReporter::report($e);
                    }
                    return $this->handler->report($e);
                }

                public function render($request, \Throwable $e)
                {
                    return $this->handler->render($request, $e);
                }
                public function renderForConsole($output, \Throwable $e)
                {
                    $this->handler->renderForConsole($output, $e);
                }
                public function shouldReport(\Throwable $e)
                {
                    return $this->handler->shouldReport($e);
                }
            };
        });
    }
}
