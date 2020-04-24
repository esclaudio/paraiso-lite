<?php

use App\Controllers\CalendarController;
use App\Controllers\AlertController;
use App\Middleware\AuthMiddleware;

$app->post('/calendar', CalendarController::class . ':index')
    ->setName('calendar.index')
    ->add(AuthMiddleware::class);

$app->post('/alerts', AlertController::class)
    ->setName('alerts')
    ->add(AuthMiddleware::class);
