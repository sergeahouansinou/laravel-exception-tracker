<?php

namespace ExceptionTracker;

use ExceptionTracker\Mail\ExceptionOccurred;
use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExceptionTracker
{
    /**
     * Handle an exception: filter, build payload, store, and notify.
     */
    public static function handle(Throwable $exception): void
    {
        try {
            if (! static::shouldTrack($exception)) {
                return;
            }

            $request = static::resolveRequest();
            $payload = PayloadBuilder::build($exception, $request);

            static::store($payload);
            static::notify($payload);
        } catch (Throwable $e) {
            Log::error('[ExceptionTracker] Internal error: ' . $e->getMessage());
        }
    }

    /**
     * Determine whether the given exception should be tracked.
     */
    protected static function shouldTrack(Throwable $exception): bool
    {
        if (! config('exception-tracker.enabled', true)) {
            return false;
        }

        $disabledEnvs = config('exception-tracker.disabled_environments', []);
        if (in_array(app()->environment(), $disabledEnvs, true)) {
            return false;
        }

        $ignored = config('exception-tracker.ignored_exceptions', []);
        foreach ($ignored as $ignoredClass) {
            if ($exception instanceof $ignoredClass) {
                return false;
            }
        }

        return true;
    }

    /**
     * Store exception in the database.
     */
    protected static function store(array $payload): void
    {
        try {
            ExceptionLog::create([
                'type' => $payload['exception']['class'],
                'message' => $payload['exception']['message'],
                'file' => $payload['exception']['file'],
                'line' => $payload['exception']['line'],
                'context' => $payload,
            ]);
        } catch (Throwable $e) {
            Log::error('[ExceptionTracker] Failed to store exception: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification.
     */
    protected static function notify(array $payload): void
    {
        if (! config('exception-tracker.email_enabled', true)) {
            return;
        }

        $recipients = config('exception-tracker.recipients', []);
        if (empty($recipients)) {
            return;
        }

        try {
            $mailable = new ExceptionOccurred($payload);
            $queueEnabled = config('exception-tracker.queue.enabled', true);

            if ($queueEnabled) {
                Mail::to($recipients)->queue($mailable);
            } else {
                Mail::to($recipients)->send($mailable);
            }
        } catch (Throwable $e) {
            Log::error('[ExceptionTracker] Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Resolve the current HTTP request if available.
     */
    protected static function resolveRequest(): ?\Illuminate\Http\Request
    {
        try {
            if (app()->runningInConsole()) {
                return null;
            }
            return request();
        } catch (Throwable $e) {
            return null;
        }
    }
}
