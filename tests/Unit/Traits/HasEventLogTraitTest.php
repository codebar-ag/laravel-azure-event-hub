<?php

use CodebarAg\LaravelEventLogs\Enums\EventLogEventEnum;
use CodebarAg\LaravelEventLogs\Enums\EventLogTypeEnum;
use CodebarAg\LaravelEventLogs\Models\EventLog;
use CodebarAg\LaravelEventLogs\Traits\HasEventLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

// Create a test model that uses the trait
class TestModel extends Model
{
    use HasEventLogTrait;

    protected $table = 'test_models';

    protected $fillable = ['name', 'email'];
}

test('trait can log created event', function () {
    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn(null);
    Auth::shouldReceive('id')->andReturn(null);

    $model = new TestModel;
    $model->name = 'John Doe';
    $model->email = 'john@example.com';

    // Save the model to trigger the created event
    $model->save();

    // Check if event log was created
    $eventLog = EventLog::where('subject_type', TestModel::class)->first();
    expect($eventLog)->not->toBeNull();
    expect($eventLog->type)->toBe(EventLogTypeEnum::MODEL);
    expect($eventLog->event)->toBe(EventLogEventEnum::CREATED);
    expect($eventLog->subject_id)->toBe($model->id);
});

test('trait can log updated event', function () {
    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn(null);
    Auth::shouldReceive('id')->andReturn(null);

    // Create and save the model first
    $model = new TestModel;
    $model->name = 'John Doe';
    $model->email = 'john@example.com';
    $model->save();

    // Update the model to trigger the updated event
    $model->name = 'Jane Doe';
    $model->save();

    // Check if event log was created
    $eventLog = EventLog::where('subject_type', TestModel::class)
        ->where('type', EventLogTypeEnum::MODEL)
        ->where('event', EventLogEventEnum::UPDATED)
        ->first();
    expect($eventLog)->not->toBeNull();
    expect($eventLog->event)->toBe(EventLogEventEnum::UPDATED);
    expect($eventLog->subject_id)->toBe($model->id);
});

test('trait can log deleted event', function () {
    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn(null);
    Auth::shouldReceive('id')->andReturn(null);

    // Create and save the model first
    $model = new TestModel;
    $model->name = 'John Doe';
    $model->email = 'john@example.com';
    $model->save();

    // Delete the model to trigger the deleted event
    $model->delete();

    // Check if event log was created
    $eventLog = EventLog::where('subject_type', TestModel::class)
        ->where('type', EventLogTypeEnum::MODEL)
        ->where('event', EventLogEventEnum::DELETED)
        ->first();
    expect($eventLog)->not->toBeNull();
    expect($eventLog->event)->toBe(EventLogEventEnum::DELETED);
    expect($eventLog->subject_id)->toBe($model->id);
});

test('trait can log restored event', function () {
    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn(null);
    Auth::shouldReceive('id')->andReturn(null);

    // Create a model with SoftDeletes trait
    $model = new class extends TestModel
    {
        use Illuminate\Database\Eloquent\SoftDeletes;
    };
    $model->name = 'John Doe';
    $model->email = 'john@example.com';
    $model->save();

    // Delete and restore to trigger the restored event
    $model->delete();
    $model->restore();

    // Check if event log was created
    $eventLog = EventLog::where('subject_type', get_class($model))
        ->where('type', EventLogTypeEnum::MODEL)
        ->where('event', EventLogEventEnum::RESTORED)
        ->first();
    expect($eventLog)->not->toBeNull();
    expect($eventLog->event)->toBe(EventLogEventEnum::RESTORED);
    expect($eventLog->subject_id)->toBe($model->id);
});

test('trait logs user information when authenticated', function () {
    // Create a proper user mock that will return the correct class name
    $user = Mockery::mock('App\Models\User');
    $user->shouldReceive('getKey')->andReturn(1);
    $user->id = 1;

    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn($user);
    Auth::shouldReceive('id')->andReturn(1);

    $model = new TestModel;
    $model->name = 'John Doe';
    $model->email = 'john@example.com';

    // Save the model to trigger the created event
    $model->save();

    // Check if event log was created with user information
    $eventLog = EventLog::where('subject_type', TestModel::class)
        ->where('type', EventLogTypeEnum::MODEL)
        ->where('event', EventLogEventEnum::CREATED)
        ->first();
    expect($eventLog)->not->toBeNull();
    // The mock class name will include Mockery prefix, so we check it contains the expected class
    expect($eventLog->user_type)->toContain('App_Models_User');
    expect($eventLog->user_id)->toBe(1);
});

test('trait handles model without id', function () {
    // Mock Auth facade
    Auth::shouldReceive('user')->andReturn(null);
    Auth::shouldReceive('id')->andReturn(null);

    $model = new TestModel;
    $model->name = 'John Doe';
    $model->email = 'john@example.com';

    // Save the model to trigger the created event
    $model->save();

    // Check if event log was created
    $eventLog = EventLog::where('subject_type', TestModel::class)
        ->where('type', EventLogTypeEnum::MODEL)
        ->where('event', EventLogEventEnum::CREATED)
        ->first();
    expect($eventLog)->not->toBeNull();
    expect($eventLog->subject_id)->toBe($model->id); // Model gets an ID when saved
    expect($eventLog->user_type)->toBeNull();
    expect($eventLog->user_id)->toBeNull();
});

test('trait skips logging when disabled', function () {
    // Ensure logging is disabled
    config()->set('laravel-event-logs.enabled', false);

    $model = new TestModel;
    $model->name = 'Disabled Case';
    $model->email = 'disabled@example.com';
    $model->save();

    // No event logs should be created when disabled
    expect(EventLog::count())->toBe(0);

    // Restore for other tests
    config()->set('laravel-event-logs.enabled', true);
});
