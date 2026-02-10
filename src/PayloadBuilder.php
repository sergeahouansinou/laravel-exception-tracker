<?php

namespace ExceptionTracker;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class PayloadBuilder
{
    /**
     * Build the full exception payload.
     */
    public static function build(Throwable $exception, ?Request $request = null): array
    {
        return [
            'exception' => self::buildException($exception),
            'request' => self::buildRequest($request),
            'user' => self::buildUser($request),
            'environment' => self::buildEnvironment(),
        ];
    }

    /**
     * Build exception details.
     */
    protected static function buildException(Throwable $exception): array
    {
        return [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => self::buildTrace($exception),
        ];
    }

    /**
     * Build a limited, readable stack trace.
     */
    protected static function buildTrace(Throwable $exception): array
    {
        $limit = config('exception-tracker.stack_trace_limit', 20);
        $trace = $exception->getTrace();

        return collect($trace)
            ->take($limit)
            ->map(function (array $frame, int $index) {
                return [
                    'index' => $index,
                    'file' => $frame['file'] ?? '[internal]',
                    'line' => $frame['line'] ?? 0,
                    'function' => ($frame['class'] ?? '') .
                        ($frame['type'] ?? '') .
                        ($frame['function'] ?? ''),
                ];
            })
            ->all();
    }

    /**
     * Build HTTP request context.
     */
    protected static function buildRequest(?Request $request): array
    {
        if (! $request) {
            return [];
        }

        $sensitiveFields = config('exception-tracker.sensitive_fields', []);

        return [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'headers' => self::filterSensitive(
                collect($request->headers->all())
                    ->map(fn ($values) => implode(', ', $values))
                    ->all(),
                $sensitiveFields
            ),
            'body' => self::filterSensitive(
                $request->all(),
                $sensitiveFields
            ),
        ];
    }

    /**
     * Build authenticated user information.
     */
    protected static function buildUser(?Request $request): array
    {
        if (! $request) {
            return [];
        }

        try {
            $user = $request->user();
            if ($user) {
                return [
                    'id' => $user->getKey(),
                    'email' => $user->email ?? null,
                ];
            }
        } catch (Throwable $e) {
            // Silently ignore auth errors
        }

        return [];
    }

    /**
     * Build environment information.
     */
    protected static function buildEnvironment(): array
    {
        return [
            'app_name' => config('app.name', 'Laravel'),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timestamp' => now()->toIso8601String(),
            'request_id' => (string) Str::uuid(),
            'server' => gethostname() ?: 'unknown',
        ];
    }

    /**
     * Mask sensitive fields in the given data.
     */
    public static function filterSensitive(array $data, array $sensitiveFields): array
    {
        $lowered = array_map('strtolower', $sensitiveFields);

        return collect($data)->map(function ($value, $key) use ($lowered) {
            if (in_array(strtolower($key), $lowered, true)) {
                return '********';
            }
            if (is_array($value)) {
                return self::filterSensitive($value, $lowered);
            }
            return $value;
        })->all();
    }
}
