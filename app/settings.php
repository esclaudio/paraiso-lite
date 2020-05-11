<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = new Dotenv;
    $dotenv->load(dirname(__DIR__) . '/.env');
}

return [
    'app_name' => get_env('APP_NAME'),
    'app_url'  => get_env('APP_URL'),
    'app_env'  => get_env('APP_ENV'),
    'debug'    => get_env('APP_DEBUG'),
    'timezone' => get_env('APP_TIMEZONE'),
    'demo'     => get_env('APP_DEMO'),
    'displayErrorDetails' => get_env('APP_DEBUG'),
    'determineRouteBeforeAppMiddleware' => true,

    'view' => [
        'templates' => '../resources/views',
        'cache'     => get_env('APP_DEBUG')? false: '../storage/views',
        'debug'     => get_env('APP_DEBUG'),
    ],

    'database' => [
        'driver'    => get_env('DB_CONNECTION'),
        'host'      => get_env('DB_HOST'),
        'port'      => get_env('DB_PORT'),
        'database'  => get_env('DB_DATABASE'),
        'username'  => get_env('DB_USERNAME'),
        'password'  => get_env('DB_PASSWORD'),
        'charset'   => get_env('DB_CHARSET'),
        'collation' => get_env('DB_COLLATION'),
        'prefix'    => get_env('DB_PREFIX'),
    ],

    'mail' => [
        'host' => get_env('MAIL_HOST'),
        'port' => get_env('MAIL_PORT'),
        'security' => get_env('MAIL_ENCRYPTION'),
        'from' => [
            'name'    => get_env('MAIL_FROM_NAME'),
            'address' => get_env('MAIL_FROM_ADDRESS'),
        ],
        'username' => get_env('MAIL_USERNAME'),
        'password' => get_env('MAIL_PASSWORD'),
    ],

    'wkhtml' => [
        'zoom' => get_env('WKHTML_ZOOM', 1),
    ],

    'unoconv' => [
        'bin' => get_env('UNOCONV_BIN', 'unoconv'),
    ],

    'redis' => [
        'host'     => get_env('REDIS_HOST', 'localhost'),
        'password' => get_env('REDIS_PASSWORD', null),
        'port'     => get_env('REDIS_PORT', 6379),
    ],

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app',
        ],

        'public' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app/public',
            'url'    => get_env('APP_URL').'/storage',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => get_env('AWS_ACCESS_KEY_ID'),
            'secret' => get_env('AWS_SECRET_ACCESS_KEY'),
            'region' => get_env('AWS_DEFAULT_REGION'),
            'bucket' => get_env('AWS_BUCKET'),
        ],

    ],
];
