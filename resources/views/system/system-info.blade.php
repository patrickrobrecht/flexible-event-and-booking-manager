@extends('layouts.app')

@section('title')
    {{ __('System information') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Application') }}</div>
                <x-bs::list>
                    <x-bs::list.item>
                        <span class="me-3">Flexible Event and Booking Manager</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('app.version') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">URL</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('app.url') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Name') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('app.name') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Owner') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('app.owner') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Language') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('app.locale') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    @php
                        $currentTime = \Carbon\Carbon::now();
                    @endphp
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Current time') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ $currentTime->getTranslatedDayName() }}, {{ $currentTime->format('d.m.Y H:i:s') }} ({{ $currentTime->getTimezone() }})</span>
                        </x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
            <div class="card mb-3">
                <div class="card-header">{{ __('API') }}</div>
                <x-bs::list>
                    @can('viewDocumentation', \App\Models\PersonalAccessToken::class)
                        <x-bs::list.item>
                            <a href="{{ route('api-docs.index') }}">{{ __('API documentation') }}</a>
                        </x-bs::list.item>
                    @endcan
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Request limit') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ formatTransChoice(':count requests', config('api.throttle.max_attempts')) }} / {{ config('api.throttle.decay_minutes') }}&nbsp;min</span>
                        </x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
            <div class="card mb-3">
                <div class="card-header">{{ __('Application server') }}</div>
                <x-bs::list>
                    @php
                        $webServer = $_SERVER['SERVER_SOFTWARE'] ?? null;
                        if (function_exists('apache_get_version') && (!isset($webServer) || trim($webServer) === 'Apache')) {
                            $apacheVersion = apache_get_version();
                            if ($apacheVersion) {
                                $webServer = 'Apache ' . $apacheVersion;
                            }
                        }
                    @endphp
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Webserver') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ $webServer ?? __('-') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('PHP version') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ phpversion() }} ({{ PHP_INT_SIZE === 8 ? '64' : '32' }}bit), {{ php_sapi_name() }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('PHP Extensions') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ implode(', ', get_loaded_extensions()) }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('PHP settings file') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ php_ini_loaded_file() ?? __('not available') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Maximum PHP execution time in seconds') }} (<code>max_execution_time</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('max_execution_time') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Time for processing the input data in seconds') }} (<code>max_input_time</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('max_input_time') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Maximum input variables') }} (<code>max_input_vars</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('max_input_vars') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('PHP memory limit') }} (<code>memory_limit</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('memory_limit') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Maximum size of PHP post data') }} (<code>post_max_size</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('post_max_size') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Maximum file size for upload') }} (<code>upload_max_filesize</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('upload_max_filesize') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('File uploads enabled') }} (<code>file_uploads</code>)</span>
                        <x-slot:end>
                            <span class="text-end">{{ ini_get('file_uploads') ? __('Yes') : __('No') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">cURL-Version</span>
                        <x-slot:end>
                            <span class="text-end">{{ function_exists('curl_version') ? curl_version()['version'] . ' ' . curl_version()['ssl_version'] : __('not available') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Database') }}</div>
                <x-bs::list>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Database name') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ DB::getDatabaseName() }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Database host') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ DB::getConfig('host') }}:{{ DB::getConfig('port') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Character set') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ DB::getConfig('collation') ?? DB::getConfig('charset') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item class="flex-wrap">
                        <span class="me-3">{{ __('Database server type and version') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ DB::getDriverTitle() }} {{ DB::getServerVersion() }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
            <div class="card mb-3">
                <div class="card-header">{{ __('Cache') }}</div>
                <x-bs::list>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Configuration cached?') }}</span>
                        <x-slot:end>{{ App::configurationIsCached() ? __('Yes') : __('No') }}</x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Events cached?') }}</span>
                        <x-slot:end>{{ App::eventsAreCached() ? __('Yes') : __('No') }}</x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Routes cached?') }}</span>
                        <x-slot:end>{{ App::routesAreCached() ? __('Yes') : __('No') }}</x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
            <div class="card mb-3">
                <div class="card-header">{{ __('Sending e-mails') }}</div>
                <x-bs::list>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Sender address') }}</span>
                        <x-slot:end>
                            <span class="text-end">{{ config('mail.from.address') }}</span>
                        </x-slot:end>
                    </x-bs::list.item>
                    <x-bs::list.item>
                        <span class="me-3">{{ __('Sending method') }}</span>
                        <x-slot:end>
                            <span class="text-end">
                                @if(config('mail.default') === 'smtp')
                                    SMTP, {{ config('mail.mailers.smtp.host') }}:{{ config('mail.mailers.smtp.port') }}, {{ config('mail.mailers.smtp.encryption') }}
                                @else
                                    {{ config('mail.default') }}
                                @endif
                            </span>
                        </x-slot:end>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
        </div>
    </div>
@endsection
