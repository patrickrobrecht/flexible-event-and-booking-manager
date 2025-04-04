<?php

return [
    'throttle' => [
        /** Fallbacks as in @see \Illuminate\Routing\Middleware\ThrottleRequests::handle() */
        'max_attempts' => env('API_THROTTLE_REQUESTS_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('API_THROTTLE_REQUESTS_DECAY_MINUTES', 1),
    ],
];
