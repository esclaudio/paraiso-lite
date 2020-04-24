<?php

use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;

$app->group('/users', function () {
    // Index
    $this->get('', UserController::class . ':index')
        ->setName('users.index');

    // Datatable
    $this->get('/datatable', UserController::class . ':datatable')
        ->setName('users.datatable');

    // Create
    $this->get('/create', UserController::class . ':create')
        ->setName('users.create');

    // Store
    $this->post('', UserController::class . ':store')
        ->setName('users.store');

    $this->group('/{user:[0-9]+}', function () {
        // View
        $this->get('', UserController::class . ':show')
            ->setName('users.show');

        // Edit
        $this->get('/edit', UserController::class . ':edit')
            ->setName('users.edit');

        // Update
        $this->put('', UserController::class . ':update')
            ->setName('users.update');
        
        // Reset password
        $this->put('/reset_password', UserController::class . ':resetPassword')
            ->setName('users.reset_password');

        // Change avatar
        $this->put('/avatar', UserController::class . ':changeAvatar')
            ->setName('users.change_avatar');

        // Delete avatar
        $this->delete('/avatar', UserController::class . ':deleteAvatar')
            ->setName('users.delete_avatar');

        // Delete
        $this->delete('', UserController::class . ':delete')
            ->setName('users.delete');
    });
})->add(AuthMiddleware::class);
