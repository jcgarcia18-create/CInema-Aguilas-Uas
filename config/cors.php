<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin requests are allowed to
    | make to your API from browser applications. The "true" value means
    | allow any domain.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8000',
        'http://localhost:8080',
        'http://10.0.2.2:8000',  // Android emulator acceso a localhost del host
        'http://10.0.2.2:8080',
        'http://127.0.0.1:8000',
        'http://127.0.0.1:8080',
    ],

    'allowed_origins_patterns' => ['localhost:*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
