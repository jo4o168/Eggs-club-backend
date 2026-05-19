<?php

$defaults = ['http://localhost:3000', 'http://127.0.0.1:3000'];

$fromEnv = array_filter(array_map(
    static fn (string $o): string => rtrim(trim($o), '/'),
    array_merge(
        [env('FRONTEND_URL', '')],
        explode(',', (string) env('CORS_EXTRA_ORIGINS', '')),
    ),
));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_merge($defaults, $fromEnv))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
