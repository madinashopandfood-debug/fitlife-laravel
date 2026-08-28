<?php

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],

    // Read from FRONTEND_ALLOWED_ORIGINS in .env — comma separated list of
    // origins that may call the API, e.g. https://fitlifebd.com. Use "*"
    // only for quick testing; lock this down for production.
    'allowed_origins' => array_filter(explode(',', env('FRONTEND_ALLOWED_ORIGINS', '*'))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
