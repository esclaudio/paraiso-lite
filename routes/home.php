<?php

use App\Middleware\AuthMiddleware;
use App\Support\Facades\Mail;
use App\Controllers\HomeController;

$app->get('/', HomeController::class . ':index')
    ->setName('home')
    ->add(AuthMiddleware::class);

$app->get('/help', HomeController::class . ':help')
    ->setName('help')
    ->add(AuthMiddleware::class);

// $app->get('/test_mail', function($request, $response) use ($container) {
//     Mail::to('test@test.com.ar')->queue(new \App\Mails\TestMail);

//     $action = \App\Models\Action::find(1);

//     if ($action) {
//         Mail::to($action->responsible)->queue(new \App\Mails\ActionCreatedMail($action));
//     }
    
//     return $response->getBody()->write('Mail sent!');
// });

// $app->get('/test_doc', function($request, $response) use ($container) {
//     $ok = $container['unoconv']->convertToPdf(FILES_PATH.'/802cb802-dec4-40d9-bc24-4856045f2c65', '/home/sistemas/preview.pdf');
//     return $response->getBody()->write($ok? 'Documento creado': 'Error');
// });
