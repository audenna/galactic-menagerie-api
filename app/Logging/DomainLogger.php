<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class DomainLogger
{
    public static function alert(string $event, array $context = []): void
    {
        Log::channel('domain')->alert($event, $context);
    }

    public static function info(string $event, array $context = []): void
    {
        Log::channel('domain')->info($event, $context);
    }

    public static function warning(string $event, array $context = []): void
    {
        Log::channel('domain')->warning($event, $context);
    }

    public static function error(string $event, array $context = []): void
    {
        Log::channel('domain')->error($event, $context);
    }
}
