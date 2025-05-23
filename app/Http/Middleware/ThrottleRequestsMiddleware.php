<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequestsMiddleware extends ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = ''): Response
    {
        /** @var int $maxAttemptsFromConfig */
        $maxAttemptsFromConfig = config('api.throttle.max_attempts', $maxAttempts);
        /** @var int $decayMinutesFromConfig */
        $decayMinutesFromConfig = config('api.throttle.decay_minutes', $decayMinutes);

        return parent::handle($request, $next, $maxAttemptsFromConfig, $decayMinutesFromConfig, $prefix);
    }
}
