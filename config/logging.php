<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'error'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    'channels' => [
        'request' => [
            'driver' => 'daily',
            'path' => storage_path('logs/request/request.log'),
            'level' => 'info',
            'days' => 2,
            'replace_placeholders' => true,
        ],

        'db' => [
            'driver' => 'daily',
            'path' => storage_path('logs/db/db.log'),
            'level' => 'notice',
            'days' => 2,
            'replace_placeholders' => true,
        ],

        'error' => [
            'driver' => 'single',
            'path' => storage_path('logs/error.log'),
            'level' => 'warning',
            'replace_placeholders' => true,
        ],

        'auth' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth/auth.log'),
            'level' => 'info',
            'days' => 14,
            'replace_placeholders' => true,
        ],
    ],

];
