<?php

use App\Controllers\NonconformityController;
use App\Middleware\AuthMiddleware;
use App\Middleware\ParamsPersistenceMiddleware;

$app->group('/nonconformities', function () {
    // Index
    $this->get('', NonconformityController::class.':index')
        ->setName('nonconformities.index');
    
    // Datatable
    $this->get('/datatable', NonconformityController::class.':datatable')
        ->setName('nonconformities.datatable');

    // Create
    $this->get('/create', NonconformityController::class.':create')
        ->setName('nonconformities.create');

    // Store
    $this->post('', NonconformityController::class.':store')
        ->setName('nonconformities.store');

    $this->group('/{nonconformity:[0-9]+}', function () {
        // Show
        $this->get('', NonconformityController::class.':show')
            ->setName('nonconformities.show');

        // Edit
        $this->get('/edit', NonconformityController::class.':edit')
            ->setName('nonconformities.edit');

        // Update
        $this->put('', NonconformityController::class.':update')
            ->setName('nonconformities.update');

        // Delete
        $this->delete('', NonconformityController::class.':destroy')
            ->setName('nonconformities.destroy');
    });
})->add(AuthMiddleware::class);
