<?php

use App\Controllers\ProductController;
use App\Middleware\AuthMiddleware;

$app->group('/products', function () {
    // Index
    $this->get('', ProductController::class . ':index')
        ->setName('products.index');

    // Datatable
    $this->get('/datatable', ProductController::class . ':datatable')
        ->setName('products.datatable');

    // Create
    $this->get('/create', ProductController::class . ':create')
        ->setName('products.create');

    // Store
    $this->post('', ProductController::class . ':store')
        ->setName('products.store');

    $this->group('/{product:[0-9]+}', function () {
        // Show
        $this->get('', ProductController::class.':show')
            ->setName('products.show');

        // Edit
        $this->get('/edit', ProductController::class . ':edit')
            ->setName('products.edit');

        // Update
        $this->put('', ProductController::class . ':update')
            ->setName('products.update');

        // Delete
        $this->delete('', ProductController::class . ':destroy')
            ->setName('products.destroy');
    });
})->add(AuthMiddleware::class);
