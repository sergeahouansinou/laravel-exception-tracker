<?php

namespace Tests\Unit;

use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ExceptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_defaults_to_15_per_page(): void
    {
        config()->set('exception-tracker.max_per_page', 100);

        $request = Request::create('/api/exception-tracker', 'GET');

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $response = $controller->index($request);
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(15, $data['data']['per_page']);
    }

    public function test_index_respects_per_page_param(): void
    {
        config()->set('exception-tracker.max_per_page', 100);

        $request = Request::create('/api/exception-tracker?per_page=5', 'GET');

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $response = $controller->index($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(5, $data['data']['per_page']);
    }

    public function test_index_caps_per_page_at_max_per_page(): void
    {
        config()->set('exception-tracker.max_per_page', 20);

        $request = Request::create('/api/exception-tracker?per_page=9999', 'GET');

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $response = $controller->index($request);
        $data = json_decode($response->getContent(), true);

        $this->assertLessThanOrEqual(20, $data['data']['per_page']);
    }

    public function test_index_per_page_minimum_is_1(): void
    {
        config()->set('exception-tracker.max_per_page', 100);

        $request = Request::create('/api/exception-tracker?per_page=0', 'GET');

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $response = $controller->index($request);
        $data = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(1, $data['data']['per_page']);
    }

    public function test_show_rejects_non_numeric_id(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $controller->show('abc');
    }

    public function test_show_rejects_zero_id(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $controller->show('0');
    }

    public function test_show_rejects_negative_id(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $controller->show('-1');
    }

    public function test_show_accepts_valid_integer_id(): void
    {
        $log = ExceptionLog::create([
            'type' => 'RuntimeException',
            'message' => 'Test',
            'file' => '/test.php',
            'line' => 1,
            'context' => [],
        ]);

        $controller = new \ExceptionTracker\Http\Controllers\ExceptionController();
        $response = $controller->show((string) $log->id);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals($log->id, $data['id']);
    }
}
