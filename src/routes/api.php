<?php

use ExceptionTracker\Http\Controllers\ExceptionController;
use Illuminate\Support\Facades\Route;

$middleware = array_filter([
    ...config('exception-tracker.route_middleware', []),
    (int) config('exception-tracker.rate_limit') > 0 ? 'throttle:' . (int) config('exception-tracker.rate_limit') . ',1' : null,
]);

Route::prefix('api/exception-tracker')
    ->middleware(array_values($middleware))
    ->group(function () {
        Route::get('/', [ExceptionController::class, 'index']);
        Route::get('/{id}', [ExceptionController::class, 'show'])->where('id', '[0-9]+');
    });
