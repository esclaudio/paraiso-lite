<?php

use App\Middleware\ParamsPersistenceMiddleware;
use App\Middleware\AuthMiddleware;
use App\Controllers\PendingDocumentController;
use App\Controllers\DocumentVersionTransitionController;
use App\Controllers\DocumentVersionController;
use App\Controllers\DocumentTypeController;
use App\Controllers\DocumentController;
use App\Controllers\CurrentDocumentController;

// Documents types

$app->group('/documents_types', function () {
    // Index
    $this->get('', DocumentTypeController::class . ':index')
        ->setName('documents_types.index');

    // Datatable
    $this->get('/datatable', DocumentTypeController::class . ':datatable')
        ->setName('documents_types.datatable');

    // Create
    $this->get('/create', DocumentTypeController::class . ':create')
        ->setName('documents_types.create');

    // Store
    $this->post('', DocumentTypeController::class . ':store')
        ->setName('documents_types.store');

    $this->group('/{document_type:[0-9]+}', function () {
        // Show
        $this->get('', DocumentTypeController::class.':show')
            ->setName('documents_types.show');

        // Edit
        $this->get('/edit', DocumentTypeController::class . ':edit')
            ->setName('documents_types.edit');

        // Update
        $this->put('', DocumentTypeController::class . ':update')
            ->setName('documents_types.update');

        // Delete
        $this->delete('', DocumentTypeController::class . ':destroy')
            ->setName('documents_types.destroy');
    });
})->add(AuthMiddleware::class);

// Documents

$app->group('/documents', function () {
    // Index
    $this->get('', DocumentController::class . ':index')
        ->setName('documents.index');
        // ->add(ParamsPersistenceMiddleware::class);

    // Datatable
    $this->get('/datatable', DocumentController::class . ':datatable')
        ->setName('documents.datatable');

    // Create
    $this->get('/create', DocumentController::class . ':create')
        ->setName('documents.create');

    // Store
    $this->post('', DocumentController::class . ':store')
        ->setName('documents.store');

    $this->group('/{document:[0-9]+}', function () {
        // View
        $this->get('', DocumentController::class . ':show')
            ->setName('documents.show');

        // Edit
        $this->get('/edit', DocumentController::class . ':edit')
            ->setName('documents.edit');

        // Update
        $this->put('', DocumentController::class . ':update')
            ->setName('documents.update');

        // Delete
        $this->delete('', DocumentController::class . ':destroy')
            ->setName('documents.destroy');
            
        // Version
        $this->group('/versions', function () {
            // Create
            $this->get('/create', DocumentVersionController::class . ':create')
                ->setName('documents_versions.create');

            // Store
            $this->post('', DocumentVersionController::class . ':store')
                ->setName('documents_versions.store');
            
            $this->group('/{version:[0-9]+}', function (){
                // View

                $this->get('/', DocumentVersionController::class . ':show')
                    ->setName('documents_versions.show');
                    
                // Edit
                $this->get('/edit', DocumentVersionController::class . ':edit')
                    ->setName('documents_versions.edit');

                // Update
                $this->put('', DocumentVersionController::class . ':update')
                    ->setName('documents_versions.update');

                // Update
                $this->post('', DocumentVersionController::class . ':periodicReview')
                    ->setName('documents_versions.periodic_review');

                // Download
                $this->get('/download', DocumentVersionController::class . ':download')
                    ->setName('documents_versions.download');

                // Preview
                $this->get('/preview', DocumentVersionController::class . ':preview')
                    ->setName('documents_versions.preview');

                // Transition
                $this->post('/transition', DocumentVersionTransitionController::class . ':store')
                    ->setName('documents_versions.transition');

                // Delete
                $this->delete('', DocumentVersionController::class . ':destroy')
                    ->setName('documents_versions.destroy');
            });
        });
    });
})->add(AuthMiddleware::class);

// Pending documents

$app->get('/pending_documents', PendingDocumentController::class . ':index')
    ->setName('pending_documents.index')
    ->add(AuthMiddleware::class);

// Current documents

$app->get('/current_documents', CurrentDocumentController::class . ':index')
    ->setName('current_documents.index')
    ->add(AuthMiddleware::class)
    ->add(ParamsPersistenceMiddleware::class);
