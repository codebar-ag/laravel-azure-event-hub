<?php

namespace CodebarAg\LaravelEventLogs\Tests;

use CodebarAg\LaravelEventLogs\LaravelEventLogsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            LaravelEventLogsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Use SQLite for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up Event Logs config for testing
        $app['config']->set('laravel-event-logs', [
            'enabled' => true,
            'providers' => [
                'azure_event_hub' => [
                    'endpoint' => env('AZURE_EVENT_HUB_ENDPOINT', 'https://test-namespace.servicebus.windows.net'),
                    'path' => env('AZURE_EVENT_HUB_PATH', 'test-event-hub'),
                    'policy_name' => env('AZURE_EVENT_HUB_POLICY_NAME', 'RootManageSharedAccessKey'),
                    'primary_key' => env('AZURE_EVENT_HUB_PRIMARY_KEY', 'test-primary-key-for-testing-only'),
                ],
            ],
            'exclude_routes' => env('EVENT_LOGS_EXCLUDE_ROUTES', []),
            'sanitize' => [
                'request_headers_exclude' => [
                    'authorization',
                    'cookie',
                    'x-csrf-token',
                ],
                'request_data_exclude' => [
                    'password',
                    'password_confirmation',
                    'token',
                ],
            ],
        ]);

        // Ensure testing environment
        $app['config']->set('app.env', 'testing');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('queue.default', 'sync');

        // Create auxiliary test tables needed by tests
        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
