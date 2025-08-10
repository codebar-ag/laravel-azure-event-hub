<?php

use CodebarAg\LaravelEventLogs\DTO\AzureEventHubDTO;
use CodebarAg\LaravelEventLogs\Enums\EventLogEventEnum;
use CodebarAg\LaravelEventLogs\Enums\EventLogTypeEnum;
use CodebarAg\LaravelEventLogs\Models\EventLog;

test('AzureEventHubDTO maps EventLog fields correctly', function () {
    $eventLog = EventLog::create([
        'uuid' => '123e4567-e89b-12d3-a456-426614174000',
        'type' => EventLogTypeEnum::HTTP,
        'subject_type' => 'App\\Models\\User',
        'subject_id' => 42,
        'user_type' => 'App\\Models\\User',
        'user_id' => 7,
        'request_route' => 'users.show',
        'request_method' => 'GET',
        'request_url' => 'https://example.com/users/42',
        'request_ip' => '127.0.0.1',
        'request_headers' => ['Accept' => 'application/json'],
        'request_data' => ['foo' => 'bar'],
        'event' => EventLogEventEnum::CREATED,
        'event_data' => ['changed' => ['name']],
        'context' => ['tenant_id' => 1],
    ]);

    $dto = AzureEventHubDTO::fromEventLog($eventLog);
    $array = $dto->toArray();

    expect($dto->uuid)->toBe('123e4567-e89b-12d3-a456-426614174000');
    expect($dto->type)->toBe(EventLogTypeEnum::HTTP);
    expect($dto->event)->toBe(EventLogEventEnum::CREATED);
    expect($dto->subject_type)->toBe('App\\Models\\User');
    expect($dto->subject_id)->toBe(42);
    expect($dto->user_type)->toBe('App\\Models\\User');
    expect($dto->user_id)->toBe(7);
    expect($dto->request_route)->toBe('users.show');
    expect($dto->request_method)->toBe('GET');
    expect($dto->request_url)->toBe('https://example.com/users/42');
    expect($dto->request_ip)->toBe('127.0.0.1');
    expect($dto->request_headers)->toBe(['Accept' => 'application/json']);
    expect($dto->request_data)->toBe(['foo' => 'bar']);
    expect($dto->event_data)->toBe(['changed' => ['name']]);
    expect($dto->context)->toBe(['tenant_id' => 1]);
    expect($dto->created_at)->not->toBeNull();

    // Validate array representation uses scalar enum values
    expect($array['uuid'])->toBe('123e4567-e89b-12d3-a456-426614174000');
    expect($array['type'])->toBe(EventLogTypeEnum::HTTP->value);
    expect($array['event'])->toBe(EventLogEventEnum::CREATED->value);
    expect($array['subject_type'])->toBe('App\\Models\\User');
    expect($array['subject_id'])->toBe(42);
    expect($array['user_type'])->toBe('App\\Models\\User');
    expect($array['user_id'])->toBe(7);
    expect($array['request_route'])->toBe('users.show');
    expect($array['request_method'])->toBe('GET');
    expect($array['request_url'])->toBe('https://example.com/users/42');
    expect($array['request_ip'])->toBe('127.0.0.1');
    expect($array['request_headers'])->toBe(['Accept' => 'application/json']);
    expect($array['request_data'])->toBe(['foo' => 'bar']);
    expect($array['event_data'])->toBe(['changed' => ['name']]);
    expect($array['context'])->toBe(['tenant_id' => 1]);
    expect($array['created_at'])->toBeString();
});
