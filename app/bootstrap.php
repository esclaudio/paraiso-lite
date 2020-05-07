<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

// Define root path

defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__));

// Constants

require __DIR__ . '/constants.php';

// Settings

if (file_exists(ROOT . '/cache/settings.php')) {
    // Use cache settings

    $settings = require ROOT . '/cache/settings.php';
} else {
    // User .env settings

    $settings = require __DIR__ . '/settings.php';
}

// Session

$sessionName = $settings['app_name'];
// Only letters, numbers, underscore or spaces
$sessionName = preg_replace('/[^0-9a-zA-Z_\s]/', '', $sessionName);
// Replace spaces with underscore
$sessionName = preg_replace('/\s+/', '_', $sessionName);
// To lower case
$sessionName = strtolower($sessionName);

session_name($sessionName);
session_cache_limiter(false); // NO delete
session_start();

// Timezone

date_default_timezone_set($settings['timezone'] ?? 'UTC');

// Instantiate the app

$app = new \Slim\App(['settings' => $settings]);
$container = $app->getContainer();

// Dependencies

require ROOT . '/app/dependencies.php';

// Messages

require ROOT . '/app/messages.php';

// Workflow

require ROOT . '/app/workflows.php';

// Routes

require ROOT . '/app/routes.php';

// Middleware

require ROOT . '/app/middleware.php';
