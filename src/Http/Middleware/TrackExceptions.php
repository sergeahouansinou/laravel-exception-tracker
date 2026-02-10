<?php

namespace ExceptionTracker\Http\Middleware;

use Closure;
use ExceptionTracker\ExceptionTracker;
use Throwable;

class TrackExceptions
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            ExceptionTracker::handle($e);

            throw $e;
        }
    }
}
