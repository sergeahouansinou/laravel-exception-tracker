<?php

if (!function_exists('exception_tracker_log')) {
    function exception_tracker_log(\Throwable $e): void
    {
        \ExceptionTracker\ExceptionTracker::handle($e);
    }
}
