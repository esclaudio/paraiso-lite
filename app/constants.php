<?php

define('PUBLIC_PATH', ROOT . '/public');
define('FILES_PATH', ROOT . '/storage/app');
define('LOGS_PATH', ROOT . '/storage/logs');

define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');
define('REMEMBER_TIME', '+1 week');

define('MIN_ANSWERS', 3);
define('DOCUMENT_NEW_DAYS', 5);
define('PASSWORD_DURATION', 60); // Months
define('DEFAULT_PASSWORD', '123456');
define('ITEMS_PER_PAGE', 10);

$versionFile =  ROOT . '/version';

define('APP_VERSION', file_exists($versionFile) ? file_get_contents($versionFile) : date('YmdHis'));
