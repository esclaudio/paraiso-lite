<?php

use App\Controllers\AttachmentController;
use App\Middleware\AuthMiddleware;

$app->group('/attachments/{model_type}/{model_id:[0-9]+}', function () {
    // Index
    $this->get('', AttachmentController::class.':index')
        ->setName('attachments.index');

    // Store
    $this->post('', AttachmentController::class.':store')
        ->setName('attachments.store');

    $this->group('/{attachment}', function () {
        // Download
        $this->get('', AttachmentController::class.':download')
            ->setName('attachments.download');

        // Update
        $this->put('', AttachmentController::class.':update')
            ->setName('attachments.update');

        // Delete
        $this->delete('', AttachmentController::class.':destroy')
            ->setName('attachments.destroy');
    });
})->add(AuthMiddleware::class);
