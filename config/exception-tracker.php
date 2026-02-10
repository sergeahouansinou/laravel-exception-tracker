<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Exception Tracking
    |--------------------------------------------------------------------------
    |
    | When set to false, the package will not capture any exceptions.
    |
    */
    'enabled' => env('EXCEPTION_TRACKER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Email Notifications
    |--------------------------------------------------------------------------
    |
    | When set to true, an email will be sent for each tracked exception.
    |
    */
    'email_enabled' => env('EXCEPTION_TRACKER_EMAIL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Notification Recipients
    |--------------------------------------------------------------------------
    |
    | A list of email addresses that will receive exception notifications.
    |
    */
    'recipients' => array_filter(
        explode(',', env('EXCEPTION_TRACKER_RECIPIENTS', ''))
    ),

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Enable queue-based email sending for non-blocking behavior.
    | You may specify the queue connection and queue name.
    |
    */
    'queue' => [
        'enabled' => env('EXCEPTION_TRACKER_QUEUE_ENABLED', true),
        'connection' => env('EXCEPTION_TRACKER_QUEUE_CONNECTION', null),
        'queue' => env('EXCEPTION_TRACKER_QUEUE_NAME', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Exceptions
    |--------------------------------------------------------------------------
    |
    | These exception classes will not be tracked or reported.
    |
    */
    'ignored_exceptions' => [
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Disabled Environments
    |--------------------------------------------------------------------------
    |
    | The package will not track exceptions in these environments.
    |
    */
    'disabled_environments' => [
        'local',
        'testing',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields
    |--------------------------------------------------------------------------
    |
    | These field names will be masked in request data, headers and body
    | to prevent sensitive information from being exposed.
    |
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'authorization',
        'cookie',
        'api_key',
        'api_secret',
        'credit_card',
        'card_number',
        'cvv',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stack Trace Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of stack trace frames to include in the payload.
    |
    */
    'stack_trace_limit' => 20,

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to retain exception logs in the database.
    |
    */
    'max_days' => 30,

];
