<?php

namespace CodebarAg\LaravelEventLogs\Enums;

enum EventLogEventEnum: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
}
