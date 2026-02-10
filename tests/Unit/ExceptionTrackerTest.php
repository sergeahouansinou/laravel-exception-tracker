<?php

namespace Tests\Unit;

use ExceptionTracker\ExceptionTracker;
use ExceptionTracker\Mail\ExceptionOccurred;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ExceptionTrackerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('exception-tracker.enabled', true);
        config()->set('exception-tracker.email_enabled', true);
        config()->set('exception-tracker.recipients', ['test@example.com']);
        config()->set('exception-tracker.queue.enabled', false);
        config()->set('exception-tracker.disabled_environments', []);
    }

    public function test_does_not_track_when_disabled(): void
    {
        config()->set('exception-tracker.enabled', false);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test'));

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_does_not_track_in_disabled_environment(): void
    {
        config()->set('exception-tracker.disabled_environments', [app()->environment()]);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test'));

        Mail::assertNothingSent();
    }

    public function test_does_not_track_ignored_exceptions(): void
    {
        Mail::fake();

        ExceptionTracker::handle(
            ValidationException::withMessages(['field' => 'error'])
        );

        Mail::assertNothingSent();
    }

    public function test_sends_email_for_tracked_exception(): void
    {
        config()->set('exception-tracker.queue.enabled', false);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test error'));

        Mail::assertSent(ExceptionOccurred::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_does_not_send_email_when_email_disabled(): void
    {
        config()->set('exception-tracker.email_enabled', false);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test'));

        Mail::assertNothingSent();
    }

    public function test_does_not_send_email_when_no_recipients(): void
    {
        config()->set('exception-tracker.recipients', []);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test'));

        Mail::assertNothingSent();
    }

    public function test_queues_email_when_queue_enabled(): void
    {
        config()->set('exception-tracker.queue.enabled', true);
        Mail::fake();

        ExceptionTracker::handle(new \RuntimeException('Test'));

        Mail::assertQueued(ExceptionOccurred::class);
    }

    public function test_fails_silently_on_internal_error(): void
    {
        Log::shouldReceive('error')
            ->atLeast()->once();

        // Force an error by setting recipients to invalid value
        config()->set('exception-tracker.recipients', 'invalid');

        ExceptionTracker::handle(new \RuntimeException('Test'));

        // No exception should be thrown - test passes if we reach here
        $this->assertTrue(true);
    }

    public function test_mailable_has_correct_subject(): void
    {
        $payload = [
            'exception' => [
                'class' => \RuntimeException::class,
                'message' => 'Test error',
                'code' => 0,
                'file' => '/test/file.php',
                'line' => 42,
                'trace' => [],
            ],
            'request' => [],
            'user' => [],
            'environment' => [
                'app_name' => 'TestApp',
                'environment' => 'production',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'timestamp' => now()->toIso8601String(),
                'request_id' => 'test-id',
                'server' => 'localhost',
            ],
        ];

        $mailable = new ExceptionOccurred($payload);
        $envelope = $mailable->envelope();

        $this->assertStringContainsString('RuntimeException', $envelope->subject);
        $this->assertStringContainsString('TestApp', $envelope->subject);
        $this->assertStringContainsString('production', $envelope->subject);
    }
}
