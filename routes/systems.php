<?php

use App\Controllers\SystemController;
use App\Middleware\AuthMiddleware;

$app->group('/systems', function () {
    // Index
    $this->get('', SystemController::class . ':index')
        ->setName('systems.index');

    // Datatable
    $this->get('/datatable', SystemController::class . ':datatable')
        ->setName('systems.datatable');

    // Create
    $this->get('/create', SystemController::class . ':create')
        ->setName('systems.create');

    // Store
    $this->post('', SystemController::class . ':store')
        ->setName('systems.store');

    $this->group('/{system:[0-9]+}', function () {
        // Show
        $this->get('', SystemController::class.':show')
            ->setName('systems.show');

        // Edit
        $this->get('/edit', SystemController::class . ':edit')
            ->setName('systems.edit');

        // Update
        $this->put('', SystemController::class . ':update')
            ->setName('systems.update');

        // Delete
        $this->delete('', SystemController::class . ':destroy')
            ->setName('systems.destroy');
    });
})->add(AuthMiddleware::class);
