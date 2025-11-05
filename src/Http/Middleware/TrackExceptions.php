<?php

namespace ExceptionTracker\Http\Middleware;

use Closure;
use ExceptionTracker\Models\ExceptionLog;
use Throwable;
use Illuminate\Support\Facades\Mail;
use ExceptionTracker\Mail\ExceptionOccurred;

class TrackExceptions
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            if (config('exception-tracker.enabled')) {
                ExceptionLog::create([
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'context' => [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'input' => $request->except(['password']),
                    ],
                ]);

                if ($email = config('exception-tracker.notify_email')) {
                    Mail::raw("Exception: {$e->getMessage()}", function ($m) use ($email) {
                        $m->to($email)->subject('Laravel Exception Tracker Alert');
                    });
                }
            }

            throw $e; // Let Laravel handle the response
        }
    }
}
