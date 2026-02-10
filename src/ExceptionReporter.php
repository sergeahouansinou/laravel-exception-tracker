<?php

namespace ExceptionTracker;

use Throwable;

/**
 * @deprecated Use ExceptionTracker::handle() instead.
 */
class ExceptionReporter
{
    public static function report(Throwable $e): void
    {
        ExceptionTracker::handle($e);
    }
}
