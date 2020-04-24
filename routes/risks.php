<?php

use App\Middleware\AuthMiddleware;
use App\Controllers\RiskTypeController;

// Risks Types

$app->group('/risks_types', function () {
    // Index
    $this->get('', RiskTypeController::class . ':index')
        ->setName('risks_types.index');

    // Datatable
    $this->get('/datatable', RiskTypeController::class . ':datatable')
        ->setName('risks_types.datatable');

    // Create
    $this->get('/create', RiskTypeController::class . ':create')
        ->setName('risks_types.create');

    // Store
    $this->post('', RiskTypeController::class . ':store')
        ->setName('risks_types.store');

    $this->group('/{risk_type:[0-9]+}', function () {
        // Show
        $this->get('', RiskTypeController::class.':show')
            ->setName('risks_types.show');

        // Edit
        $this->get('/edit', RiskTypeController::class . ':edit')
            ->setName('risks_types.edit');

        // Update
        $this->put('', RiskTypeController::class . ':update')
            ->setName('risks_types.update');

        // Delete
        $this->delete('', RiskTypeController::class . ':destroy')
            ->setName('risks_types.destroy');
    });
})->add(AuthMiddleware::class);
