<?php

use App\Controllers\CustomerController;
use App\Middleware\AuthMiddleware;

$app->group('/customers', function () {
    // Index
    $this->get('', CustomerController::class . ':index')
        ->setName('customers.index');

    // Datatable
    $this->get('/datatable', CustomerController::class . ':datatable')
        ->setName('customers.datatable');

    // Create
    $this->get('/create', CustomerController::class . ':create')
        ->setName('customers.create');

    // Store
    $this->post('', CustomerController::class . ':store')
        ->setName('customers.store');

    $this->group('/{customer:[0-9]+}', function () {
        // Show
        $this->get('', CustomerController::class.':show')
            ->setName('customers.show');

        // Edit
        $this->get('/edit', CustomerController::class . ':edit')
            ->setName('customers.edit');

        // Update
        $this->put('', CustomerController::class . ':update')
            ->setName('customers.update');

        // Delete
        $this->delete('', CustomerController::class . ':destroy')
            ->setName('customers.destroy');
    });
})->add(AuthMiddleware::class);
