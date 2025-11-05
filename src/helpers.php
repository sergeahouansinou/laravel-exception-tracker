<?php

if (!function_exists('exception_tracker_log')) {
    function exception_tracker_log(\Throwable $e)
    {
        \ExceptionTracker\Models\ExceptionLog::create([
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => [],
        ]);
    }
}
