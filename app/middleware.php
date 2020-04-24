<?php

// $app->add(App\Middleware\LocaleMiddleware::class);
$app->add(App\Middleware\TrimNullMiddleware::class);
$app->add(new RKA\Middleware\IpAddress(true, ['10.0.0.1', '10.0.0.2']));
$app->add($container['csrf']);