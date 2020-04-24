<?php

use App\Controllers\RoleController;
use App\Middleware\AuthMiddleware;

$app->group('/roles', function () {
    // Index
    $this->get('', RoleController::class . ':index')
        ->setName('roles.index');

    // Datatable
    $this->get('/datatable', RoleController::class . ':datatable')
        ->setName('roles.datatable');

    // Create
    $this->get('/create', RoleController::class . ':create')
        ->setName('roles.create');

    // Store
    $this->post('', RoleController::class . ':store')
        ->setName('roles.store');

    $this->group('/{role:[0-9]+}', function () {
        // Show
        $this->get('', RoleController::class.':show')
            ->setName('roles.show');

        // Edit
        $this->get('/edit', RoleController::class . ':edit')
            ->setName('roles.edit');

        // Update
        $this->put('', RoleController::class . ':update')
            ->setName('roles.update');

        // Delete
        $this->delete('', RoleController::class . ':destroy')
            ->setName('roles.destroy');
    });
})->add(AuthMiddleware::class);
