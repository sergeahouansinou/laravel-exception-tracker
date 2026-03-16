<?php

namespace Tests\Unit;

use ExceptionTracker\Commands\ClearOldExceptions;
use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClearOldExceptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_logs_older_than_max_days(): void
    {
        config()->set('exception-tracker.max_days', 30);

        $old = ExceptionLog::create([
            'type' => 'RuntimeException',
            'message' => 'Old',
            'file' => '/test.php',
            'line' => 1,
            'context' => [],
        ]);
        $old->created_at = now()->subDays(31);
        $old->save();

        ExceptionLog::create([
            'type' => 'RuntimeException',
            'message' => 'New',
            'file' => '/test.php',
            'line' => 1,
            'context' => [],
        ]);

        $this->artisan('exception-tracker:clear')
            ->assertExitCode(0);

        $this->assertEquals(1, ExceptionLog::count());
        $this->assertEquals('New', ExceptionLog::first()->message);
    }

    public function test_skips_cleanup_when_max_days_is_zero(): void
    {
        config()->set('exception-tracker.max_days', 0);

        $old = ExceptionLog::create([
            'type' => 'RuntimeException',
            'message' => 'Old',
            'file' => '/test.php',
            'line' => 1,
            'context' => [],
        ]);
        $old->created_at = now()->subDays(31);
        $old->save();

        $this->artisan('exception-tracker:clear')
            ->assertExitCode(0);

        // Nothing should be deleted when max_days is 0
        $this->assertEquals(1, ExceptionLog::count());
    }

    public function test_skips_cleanup_when_max_days_is_negative(): void
    {
        config()->set('exception-tracker.max_days', -5);

        ExceptionLog::create([
            'type' => 'RuntimeException',
            'message' => 'Old',
            'file' => '/test.php',
            'line' => 1,
            'context' => [],
        ]);

        $this->artisan('exception-tracker:clear')
            ->assertExitCode(0);

        // Nothing should be deleted when max_days is invalid
        $this->assertEquals(1, ExceptionLog::count());
    }
}
