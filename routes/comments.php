<?php

use App\Controllers\CommentController;
use App\Middleware\AuthMiddleware;

$app->group('/comments/{model_type}/{model_id:[0-9]+}', function () {
    // Index
    $this->get('', CommentController::class.':index')
        ->setName('comments.index');

    // Store
    $this->post('', CommentController::class.':store')
        ->setName('comments.store');

    // Delete
    $this->delete('/{comment:[0-9]+}', CommentController::class.':destroy')
        ->setName('comments.destroy');
})->add(AuthMiddleware::class);
