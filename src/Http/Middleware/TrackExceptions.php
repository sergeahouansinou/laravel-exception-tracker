<?php

namespace ExceptionTracker\Http\Middleware;

use Closure;
use ExceptionTracker\ExceptionTracker;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackExceptions
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            ExceptionTracker::handle($e);

            throw $e;
        }
    }
}
