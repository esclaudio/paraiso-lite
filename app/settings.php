<?php

if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = Dotenv\Dotenv::createMutable(dirname(__DIR__));
    $dotenv->load();
}

return [
    'app_name' => getenv('APP_NAME'),
    'app_url'  => getenv('APP_URL'),
    'app_env'  => getenv('APP_ENV'),
    'debug'    => getenv('APP_DEBUG'),
    'help_url' => getenv('HELP_URL'),
    'displayErrorDetails' => getenv('APP_DEBUG'),
    'determineRouteBeforeAppMiddleware' => true,

    'view' => [
        'templates' => '../resources/views',
        'cache'     => getenv('APP_DEBUG')? false: '../storage/views',
        'debug'     => getenv('APP_DEBUG'),
    ],

    'database' => [
        'driver'    => getenv('DB_CONNECTION'),
        'host'      => getenv('DB_HOST'),
        'port'      => getenv('DB_PORT'),
        'database'  => getenv('DB_DATABASE'),
        'username'  => getenv('DB_USERNAME'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => getenv('DB_CHARSET'),
        'collation' => getenv('DB_COLLATION'),
        'prefix'    => getenv('DB_PREFIX'),
    ],

    'mail' => [
        'host' => getenv('MAIL_HOST'),
        'port' => getenv('MAIL_PORT'),
        'security' => getenv('MAIL_ENCRYPTION'),
        'from' => [
            'name'    => getenv('MAIL_FROM_NAME'),
            'address' => getenv('MAIL_FROM_ADDRESS'),
        ],
        'username' => getenv('MAIL_USERNAME'),
        'password' => getenv('MAIL_PASSWORD'),
    ],

    'wkhtml' => [
        'zoom' => getenv('WKHTML_ZOOM', 1),
    ],

    'unoconv' => [
        'bin' => getenv('UNOCONV_BIN', 'unoconv'),
    ],

    'redis' => [
        'host'     => getenv('REDIS_HOST', 'localhost'),
        'password' => getenv('REDIS_PASSWORD', null),
        'port'     => getenv('REDIS_PORT', 6379),
    ],

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app',
        ],

        'public' => [
            'driver' => 'local',
            'root'   => ROOT . '/storage/app/public',
            'url'    => getenv('APP_URL').'/storage',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => getenv('AWS_ACCESS_KEY_ID'),
            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            'region' => getenv('AWS_DEFAULT_REGION'),
            'bucket' => getenv('AWS_BUCKET'),
        ],

    ],
];
