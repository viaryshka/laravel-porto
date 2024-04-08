<?php

return [
    'api' => [
        'url' => env('API_URL'),
        'prefix' => env('API_PREFIX', '/api'),
        'enable_version_prefix' => true,

        'throttle' => [
            'enabled' => env('GLOBAL_API_RATE_LIMIT_ENABLED', true),
            'attempts' => env('GLOBAL_API_RATE_LIMIT_ATTEMPTS_PER_MIN', '30'),
            'expires' => env('GLOBAL_API_RATE_LIMIT_EXPIRES_IN_MIN', '1'),
        ],

    ],
];
