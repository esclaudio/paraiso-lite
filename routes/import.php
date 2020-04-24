<?php

use App\Controllers\ImportController;
use App\Middleware\AuthMiddleware;

$app->group('/import/{model}', function () {
    // Create
    $this->get('', ImportController::class.':create')
        ->setName('import.create');
        
    // Store
    $this->post('', ImportController::class.':store')
        ->setName('import.store');
    
    // Download
    $this->get('/download', ImportController::class.':download')
            ->setName('import.download');

    $this->group('/{filename}', function () {
        // View
        $this->get('', ImportController::class.':view')
            ->setName('import.view');
            
        // Update
        $this->post('', ImportController::class.':import')
            ->setName('import.import');
    });
})->add(AuthMiddleware::class);
