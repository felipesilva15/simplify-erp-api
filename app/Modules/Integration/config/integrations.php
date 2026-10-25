<?php

return [
    'http' => [
        'timeout' => env('INTEGRATIONS_HTTP_TIMEOUT', 30),
        'retries' => env('INTEGRATIONS_HTTP_RETRIES', 0),
        'retry_sleep_ms' => env('INTEGRATIONS_HTTP_RETRY_SLEEP_MS', 200),
    ],
    'logging' => [
        'retention_days' => env('INTEGRATIONS_LOG_RETENTION_DAYS', 30),
        'sensitive_keys' => [
            'password', 'senha', 'token', 'access_token', 'refresh_token',
            'authorization', 'auth', 'secret', 'client_secret', 'api_key',
            'apikey', 'key', 'private_key', 'cert', 'certificate', 'credentials',
            'card_number', 'cvv', 'cvc', 'pan',
        ],
        'max_request_size' => 10000,
        'max_response_size' => 10000,
    ],
    'cache' => [
        'prefix' => 'integrations',
    ],
];
