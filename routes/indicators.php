<?php

use App\Middleware\AuthMiddleware;
use App\Controllers\IndicatorRecordController;
use App\Controllers\IndicatorController;

$app->group('/indicators', function () {
    // Index
    $this->get('', IndicatorController::class . ':index')
        ->setName('indicators.index');

    // Datatable
    $this->get('/datatable', IndicatorController::class . ':datatable')
        ->setName('indicators.datatable');

    // Create
    $this->get('/create', IndicatorController::class . ':create')
        ->setName('indicators.create');

    // Store
    $this->post('', IndicatorController::class . ':store')
        ->setName('indicators.store');
    
    $this->group('/{indicator:[0-9]+}', function () {
        // View
        $this->get('', IndicatorController::class.':show')
            ->setName('indicators.show');

        // Edit
        $this->get('/edit', IndicatorController::class . ':edit')
            ->setName('indicators.edit');

        // Update
        $this->put('', IndicatorController::class . ':update')
            ->setName('indicators.update');

        // Delete
        $this->delete('', IndicatorController::class . ':destroy')
            ->setName('indicators.destroy');

        // Deactivate
        $this->put('/deactivate', IndicatorController::class . ':deactivate')
            ->setName('indicators.deactivate');
        
        // Activate
        $this->put('/activate', IndicatorController::class . ':activate')
            ->setName('indicators.activate');

        // Chart
        $this->get('/chart', IndicatorController::class . ':chart')
            ->setName('indicators.chart');

        // Records
        $this->group('/record', function () {
            // Datatable
            $this->get('/datatable', IndicatorRecordController::class . ':datatable')
                ->setName('indicators_record.datatable');

            // Store
            $this->post('', IndicatorRecordController::class . ":store")
                ->setName("indicators_record.store");

            // Update
            $this->put('/{record:[0-9]+}', IndicatorRecordController::class . ":update")
                ->setName("indicators_record.update");
        });
    });
})->add(AuthMiddleware::class);
