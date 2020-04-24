<?php

use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

// Login
$app->map(['GET', 'POST'], '/login', AuthController::class.':login')
    ->setName('login')
    ->add(AuthMiddleware::class);

// Change password
$app->map(['GET', 'POST'], '/change_password', AuthController::class.':changePassword')
    ->setName('change_password')
    ->add(AuthMiddleware::class);

// Logout
$app->get('/logout', AuthController::class.':logout')
    ->setName('logout');
