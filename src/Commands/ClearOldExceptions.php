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
        $days = (int) config('exception-tracker.max_days', 30);

        if ($days < 1) {
            $this->warn('exception-tracker.max_days must be a positive integer. Skipping cleanup.');
            return;
        }

        $deleted = ExceptionLog::where('created_at', '<', now()->subDays($days))->delete();
        $this->info("$deleted old exception logs deleted.");
    }
}
