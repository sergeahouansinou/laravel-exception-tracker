<?php

namespace ExceptionTracker\Commands;

use Illuminate\Console\Command;
use ExceptionTracker\Models\ExceptionLog;

class ClearOldExceptions extends Command
{
    protected $signature = 'exception-tracker:clear';
    protected $description = 'Clear old exception logs based on config days limit';

    public function handle()
    {
        $days = config('exception-tracker.max_days');
        $deleted = ExceptionLog::where('created_at', '<', now()->subDays($days))->delete();
        $this->info("$deleted old exception logs deleted.");
    }
}
