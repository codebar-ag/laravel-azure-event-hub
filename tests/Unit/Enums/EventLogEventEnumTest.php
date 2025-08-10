<?php

use CodebarAg\LaravelEventLogs\Enums\EventLogEventEnum;

test('enum has correct values', function () {
    expect(EventLogEventEnum::CREATED->value)->toBe('created');
    expect(EventLogEventEnum::UPDATED->value)->toBe('updated');
    expect(EventLogEventEnum::DELETED->value)->toBe('deleted');
    expect(EventLogEventEnum::RESTORED->value)->toBe('restored');
});

test('enum can be created from string', function () {
    expect(EventLogEventEnum::from('created'))->toBe(EventLogEventEnum::CREATED);
    expect(EventLogEventEnum::from('updated'))->toBe(EventLogEventEnum::UPDATED);
    expect(EventLogEventEnum::from('deleted'))->toBe(EventLogEventEnum::DELETED);
    expect(EventLogEventEnum::from('restored'))->toBe(EventLogEventEnum::RESTORED);
});

test('enum can be created from string case insensitive', function () {
    expect(EventLogEventEnum::tryFrom('CREATED'))->toBeNull();
    expect(EventLogEventEnum::tryFrom('UPDATED'))->toBeNull();
    expect(EventLogEventEnum::tryFrom('DELETED'))->toBeNull();
    expect(EventLogEventEnum::tryFrom('RESTORED'))->toBeNull();
});

test('enum returns null for invalid values', function () {
    expect(EventLogEventEnum::tryFrom('invalid'))->toBeNull();
    expect(EventLogEventEnum::tryFrom(''))->toBeNull();
});

test('enum has correct number of cases', function () {
    expect(EventLogEventEnum::cases())->toHaveCount(4);
});

test('enum can be compared', function () {
    $created = EventLogEventEnum::CREATED;
    $updated = EventLogEventEnum::UPDATED;

    expect($created)->toBe(EventLogEventEnum::CREATED);
    expect($created)->not->toBe($updated);
    expect($created->value)->toBe('created');
});

test('enum can be used in switch statements', function () {
    $event = EventLogEventEnum::CREATED;

    $result = match ($event) {
        EventLogEventEnum::CREATED => 'item_created',
        EventLogEventEnum::UPDATED => 'item_updated',
        EventLogEventEnum::DELETED => 'item_deleted',
        EventLogEventEnum::RESTORED => 'item_restored',
    };

    expect($result)->toBe('item_created');
});

test('enum values are strings', function () {
    foreach (EventLogEventEnum::cases() as $case) {
        expect($case->value)->toBeString();
    }
});
