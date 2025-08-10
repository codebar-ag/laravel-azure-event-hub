<?php

namespace CodebarAg\LaravelEventLogs\Observers;

use CodebarAg\LaravelEventLogs\Enums\EventLogTypeEnum;
use CodebarAg\LaravelEventLogs\Models\EventLog;
use Illuminate\Support\Str;

class EventLogObserver
{
    public function creating(EventLog $eventLog): void
    {
        $eventLog->uuid ??= Str::uuid()->toString();
        $eventLog->type ??= EventLogTypeEnum::DEFAULT;
    }
}
