<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = new Dotenv;
    $dotenv->load(dirname(__DIR__) . '/.env');
}

return [
    'app_name' => genv('APP_NAME'),
    'app_url'  => genv('APP_URL'),
    'app_env'  => genv('APP_ENV'),
    'debug'    => genv('APP_DEBUG'),
    'timezone' => genv('APP_TIMEZONE'),
    'displayErrorDetails' => genv('APP_DEBUG'),
    'determineRouteBeforeAppMiddleware' => true,

    'view' => [
        'templates' => '../resources/views',
        'cache'     => genv('APP_DEBUG')? false: '../storage/views',
        'debug'     => genv('APP_DEBUG'),
    ],

    'database' => [
        'driver'    => genv('DB_CONNECTION'),
        'host'      => genv('DB_HOST'),
        'port'      => genv('DB_PORT'),
        'database'  => genv('DB_DATABASE'),
        'username'  => genv('DB_USERNAME'),
        'password'  => genv('DB_PASSWORD'),
        'charset'   => genv('DB_CHARSET'),
        'collation' => genv('DB_COLLATION'),
        'prefix'    => genv('DB_PREFIX'),
    ],

    'mail' => [
        'host' => genv('MAIL_HOST'),
        'port' => genv('MAIL_PORT'),
        'security' => genv('MAIL_ENCRYPTION'),
        'from' => [
            'name'    => genv('MAIL_FROM_NAME'),
            'address' => genv('MAIL_FROM_ADDRESS'),
        ],
        'username' => genv('MAIL_USERNAME'),
        'password' => genv('MAIL_PASSWORD'),
    ],

    'wkhtml' => [
        'zoom' => genv('WKHTML_ZOOM', 1),
    ],

    'unoconv' => [
        'bin' => genv('UNOCONV_BIN', 'unoconv'),
    ],

    'redis' => [
        'host'     => genv('REDIS_HOST', 'localhost'),
        'password' => genv('REDIS_PASSWORD', null),
        'port'     => genv('REDIS_PORT', 6379),
    ],

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app',
        ],

        'public' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app/public',
            'url'    => genv('APP_URL').'/storage',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => genv('AWS_ACCESS_KEY_ID'),
            'secret' => genv('AWS_SECRET_ACCESS_KEY'),
            'region' => genv('AWS_DEFAULT_REGION'),
            'bucket' => genv('AWS_BUCKET'),
        ],

    ],
];
