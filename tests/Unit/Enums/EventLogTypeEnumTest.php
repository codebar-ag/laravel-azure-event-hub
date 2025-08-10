<?php

use CodebarAg\LaravelEventLogs\Enums\EventLogTypeEnum;

test('enum has correct values', function () {
    expect(EventLogTypeEnum::DEFAULT->value)->toBe('default');
    expect(EventLogTypeEnum::HTTP->value)->toBe('http');
    expect(EventLogTypeEnum::MODEL->value)->toBe('model');
    expect(EventLogTypeEnum::CUSTOM->value)->toBe('custom');
});

test('enum can be created from string', function () {
    expect(EventLogTypeEnum::from('default'))->toBe(EventLogTypeEnum::DEFAULT);
    expect(EventLogTypeEnum::from('http'))->toBe(EventLogTypeEnum::HTTP);
    expect(EventLogTypeEnum::from('model'))->toBe(EventLogTypeEnum::MODEL);
    expect(EventLogTypeEnum::from('custom'))->toBe(EventLogTypeEnum::CUSTOM);
});

test('enum can be created from string case insensitive', function () {
    expect(EventLogTypeEnum::tryFrom('DEFAULT'))->toBeNull();
    expect(EventLogTypeEnum::tryFrom('HTTP'))->toBeNull();
    expect(EventLogTypeEnum::tryFrom('MODEL'))->toBeNull();
    expect(EventLogTypeEnum::tryFrom('CUSTOM'))->toBeNull();
});

test('enum returns null for invalid values', function () {
    expect(EventLogTypeEnum::tryFrom('invalid'))->toBeNull();
    expect(EventLogTypeEnum::tryFrom(''))->toBeNull();
});

test('enum has correct number of cases', function () {
    expect(EventLogTypeEnum::cases())->toHaveCount(4);
});

test('enum can be compared', function () {
    $default = EventLogTypeEnum::DEFAULT;
    $http = EventLogTypeEnum::HTTP;

    expect($default)->toBe(EventLogTypeEnum::DEFAULT);
    expect($default)->not->toBe($http);
    expect($default->value)->toBe('default');
});

test('enum can be used in switch statements', function () {
    $type = EventLogTypeEnum::MODEL;

    $result = match ($type) {
        EventLogTypeEnum::DEFAULT => 'default_type',
        EventLogTypeEnum::HTTP => 'http_type',
        EventLogTypeEnum::MODEL => 'model_type',
        EventLogTypeEnum::CUSTOM => 'custom_type',
    };

    expect($result)->toBe('model_type');
});

test('enum values are strings', function () {
    foreach (EventLogTypeEnum::cases() as $case) {
        expect($case->value)->toBeString();
    }
});
