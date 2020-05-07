<?php

if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = new Symfony\Component\Dotenv\Dotenv();
    $dotenv->load(dirname(__DIR__) . '/.env');
}

return [
    'app_name' => env('APP_NAME'),
    'app_url'  => env('APP_URL'),
    'app_env'  => env('APP_ENV'),
    'debug'    => env('APP_DEBUG'),
    'timezone' => env('APP_TIMEZONE'),
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

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app',
        ],

        'public' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app/public',
            'url'    => env('APP_URL').'/storage',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],
];
