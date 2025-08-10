<?php

namespace CodebarAg\LaravelEventLogs\Enums;

enum EventLogTypeEnum: string
{
    case DEFAULT = 'default';
    case HTTP = 'http';
    case MODEL = 'model';
    case CUSTOM = 'custom';
}
