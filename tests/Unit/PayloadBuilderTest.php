<?php

namespace Tests\Unit;

use ExceptionTracker\PayloadBuilder;
use Illuminate\Http\Request;
use RuntimeException;
use Tests\TestCase;

class PayloadBuilderTest extends TestCase
{
    public function test_builds_exception_payload(): void
    {
        $exception = new RuntimeException('Test error', 42);
        $payload = PayloadBuilder::build($exception);

        $this->assertArrayHasKey('exception', $payload);
        $this->assertArrayHasKey('request', $payload);
        $this->assertArrayHasKey('user', $payload);
        $this->assertArrayHasKey('environment', $payload);

        $this->assertEquals(RuntimeException::class, $payload['exception']['class']);
        $this->assertEquals('Test error', $payload['exception']['message']);
        $this->assertEquals(42, $payload['exception']['code']);
        $this->assertNotEmpty($payload['exception']['file']);
        $this->assertIsInt($payload['exception']['line']);
        $this->assertIsArray($payload['exception']['trace']);
    }

    public function test_limits_stack_trace_frames(): void
    {
        $exception = new RuntimeException('Trace test');
        $payload = PayloadBuilder::build($exception);

        $limit = config('exception-tracker.stack_trace_limit', 20);
        $this->assertLessThanOrEqual($limit, count($payload['exception']['trace']));
    }

    public function test_filters_sensitive_fields(): void
    {
        $data = [
            'username' => 'john',
            'password' => 'secret123',
            'token' => 'abc123',
            'email' => 'john@example.com',
        ];

        $sensitiveFields = ['password', 'token'];
        $filtered = PayloadBuilder::filterSensitive($data, $sensitiveFields);

        $this->assertEquals('john', $filtered['username']);
        $this->assertEquals('********', $filtered['password']);
        $this->assertEquals('********', $filtered['token']);
        $this->assertEquals('john@example.com', $filtered['email']);
    }

    public function test_filters_sensitive_fields_case_insensitive(): void
    {
        $data = [
            'Password' => 'secret123',
            'TOKEN' => 'abc123',
        ];

        $sensitiveFields = ['password', 'token'];
        $filtered = PayloadBuilder::filterSensitive($data, $sensitiveFields);

        $this->assertEquals('********', $filtered['Password']);
        $this->assertEquals('********', $filtered['TOKEN']);
    }

    public function test_filters_nested_sensitive_fields(): void
    {
        $data = [
            'user' => [
                'name' => 'john',
                'password' => 'secret',
            ],
        ];

        $sensitiveFields = ['password'];
        $filtered = PayloadBuilder::filterSensitive($data, $sensitiveFields);

        $this->assertEquals('john', $filtered['user']['name']);
        $this->assertEquals('********', $filtered['user']['password']);
    }

    public function test_builds_empty_request_when_null(): void
    {
        $exception = new RuntimeException('Test');
        $payload = PayloadBuilder::build($exception, null);

        $this->assertEmpty($payload['request']);
        $this->assertEmpty($payload['user']);
    }

    public function test_trace_frame_structure(): void
    {
        $exception = new RuntimeException('Trace structure test');
        $payload = PayloadBuilder::build($exception);

        if (!empty($payload['exception']['trace'])) {
            $frame = $payload['exception']['trace'][0];
            $this->assertArrayHasKey('index', $frame);
            $this->assertArrayHasKey('file', $frame);
            $this->assertArrayHasKey('line', $frame);
            $this->assertArrayHasKey('function', $frame);
        }
    }
}
