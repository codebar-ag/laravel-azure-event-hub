<?php

namespace CodebarAg\LaravelEventLogs\Models;

use CodebarAg\LaravelEventLogs\Database\Factories\EventLogFactory;
use CodebarAg\LaravelEventLogs\DTO\AzureEventHubDTO;
use CodebarAg\LaravelEventLogs\Enums\EventLogEventEnum;
use CodebarAg\LaravelEventLogs\Enums\EventLogTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property EventLogTypeEnum|null $type
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property string|null $request_ip
 * @property string|null $request_method
 * @property string|null $request_url
 * @property string|null $request_route
 * @property array<string, mixed>|null $request_headers
 * @property array<string, mixed>|null $request_data
 * @property EventLogEventEnum|null $event
 * @property array<string, mixed>|null $event_data
 * @property array<string, mixed>|null $context
 * @property \Carbon\Carbon|null $synced_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @use HasFactory<EventLogFactory>
 */
class EventLog extends Model
{
    /**
     * @use HasFactory<EventLogFactory>
     */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'type',
        'subject_type',
        'subject_id',
        'user_type',
        'user_id',
        'request_route',
        'request_method',
        'request_url',
        'request_ip',
        'request_headers',
        'request_data',
        'event',
        'event_data',
        'context',
        'synced_at',
        'synced_failed_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'request_headers' => 'array',
        'request_data' => 'array',
        'type' => EventLogTypeEnum::class,
        'event' => EventLogEventEnum::class,
        'event_data' => 'array',
        'context' => 'array',
        'synced_at' => 'datetime',
        'synced_failed_at' => 'datetime',
    ];

    /**
     * @param  Builder<EventLog>  $query
     * @return Builder<EventLog>
     */
    public function scopeUnsynced(Builder $query): Builder
    {
        /** @var Builder<EventLog> */
        return $query->whereNull('synced_failed_at')->whereNull('synced_at');
    }

    public function toArray(): array
    {
        return AzureEventHubDTO::fromEventLog($this)->toArray();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): EventLogFactory
    {
        return EventLogFactory::new();
    }
}
