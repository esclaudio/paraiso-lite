<?php

use App\Controllers\ProcessController;
use App\Middleware\AuthMiddleware;

$app->group('/processes', function () {
    // Index
    $this->get('', ProcessController::class . ':index')
        ->setName('processes.index');

    // Datatable
    $this->get('/datatable', ProcessController::class . ':datatable')
        ->setName('processes.datatable');

    // Create
    $this->get('/create', ProcessController::class . ':create')
        ->setName('processes.create');

    // Store
    $this->post('', ProcessController::class . ':store')
        ->setName('processes.store');

    $this->group('/{process:[0-9]+}', function () {
        // Show
        $this->get('', ProcessController::class.':show')
            ->setName('processes.show');

        // Edit
        $this->get('/edit', ProcessController::class . ':edit')
            ->setName('processes.edit');

        // Update
        $this->put('', ProcessController::class . ':update')
            ->setName('processes.update');

        // Delete
        $this->delete('', ProcessController::class . ':destroy')
            ->setName('processes.destroy');
    });
})->add(AuthMiddleware::class);
