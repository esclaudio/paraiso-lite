<?php

if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = new Dotenv\Dotenv(dirname(__DIR__));
    $dotenv->load();
}

return [
    'app_name' => env('APP_NAME'),
    'app_url'  => env('APP_URL'),
    'app_env'  => env('APP_ENV'),
    'debug'    => env('APP_DEBUG'),
    'help_url' => env('HELP_URL'),
    'displayErrorDetails' => env('APP_DEBUG'),
    'determineRouteBeforeAppMiddleware' => true,

    'view' => [
        'templates' => '../resources/views',
        'cache'     => env('APP_DEBUG')? false: '../storage/views',
        'debug'     => env('APP_DEBUG'),
    ],

    'database' => [
        'driver'    => env('DB_CONNECTION'),
        'host'      => env('DB_HOST'),
        'port'      => env('DB_PORT'),
        'database'  => env('DB_DATABASE'),
        'username'  => env('DB_USERNAME'),
        'password'  => env('DB_PASSWORD'),
        'charset'   => env('DB_CHARSET'),
        'collation' => env('DB_COLLATION'),
        'prefix'    => env('DB_PREFIX'),
    ],

    'mail' => [
        'host' => env('MAIL_HOST'),
        'port' => env('MAIL_PORT'),
        'security' => env('MAIL_ENCRYPTION'),
        'from' => [
            'name'    => env('MAIL_FROM_NAME'),
            'address' => env('MAIL_FROM_ADDRESS'),
        ],
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
    ],

    'wkhtml' => [
        'zoom' => env('WKHTML_ZOOM', 1),
    ],

    'unoconv' => [
        'bin' => env('UNOCONV_BIN', 'unoconv'),
    ],

    'redis' => [
        'host'     => env('REDIS_HOST', 'localhost'),
        'password' => env('REDIS_PASSWORD', null),
        'port'     => env('REDIS_PORT', 6379),
    ],
];
