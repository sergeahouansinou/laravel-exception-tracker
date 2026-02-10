<?php

use ExceptionTracker\Http\Controllers\ExceptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/exception-tracker')
    ->group(function () {
        Route::get('/', [ExceptionController::class, 'index']);
        Route::get('/{id}', [ExceptionController::class, 'show']);
    });
