<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Cache;

class CacheOpenApiDocListener
{
    private const COMMANDS = [
        'clear-compiled',
        'config:cache',
        'config:clear',
        'optimize',
    ];

    public function handle(CommandFinished $event): void
    {
        if (in_array($event->command, self::COMMANDS, true)) {
            Cache::put('open-api-spec', self::cacheConfigurationFile());
        }
    }

    public static function cacheConfigurationFile(): string
    {
        $replacements = [
            '{{APP_NAME}}' => config('app.name'),
            '{{API_ROOT_URL}}' => url('api'),
            '{{API_SERVER_DESCRIPTION}}' => match (config('app.env')) {
                'production' => 'production environment (uses live data)',
                'local' => 'development environment',
                default => 'staging environment',
            },
            '{{API_THROTTLE_REQUESTS_MAX_ATTEMPTS}}' => config('api.throttle.max_attempts'),
            '{{API_THROTTLE_REQUESTS_DECAY_MINUTES}}' => config('api.throttle.decay_minutes'),
            '{{APP_VERSION}}' => config('app.version'),
        ];
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            file_get_contents(base_path('open-api.yaml'))
        );
    }
}
