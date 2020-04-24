<?php

require_once './vendor/autoload.php';

$container = new Pimple\Container;

// Settings

$container['settings'] = require './app/settings.php';

// Mailer

$container['mailer'] = function ($c) {
    $settings = $c['settings'];
    // Transport

    $transport = new Swift_SmtpTransport(
        $settings['mail']['host'],
        $settings['mail']['port'],
        $settings['mail']['security']
    );

    $transport->setUsername($settings['mail']['username']);
    $transport->setPassword($settings['mail']['password']);

    // Twig

    $twig = new Slim\Views\Twig($settings['view']['templates'], [
        'cache' => $settings['view']['cache'],
        'debug' => $settings['view']['debug'],
    ]);

    // $env = $twig->getEnvironment();
    // $env->addGlobal('app_name', $settings['app_name']);

    // Mailer

    $mailer = new App\Mailer\Mailer(
        new Swift_Mailer($transport),
        $twig,
        new App\Queue\NullQueue
    );

    $mailer->alwaysFrom($settings['mail']['from']['address'], $settings['mail']['from']['name']);

    return $mailer;
};

// Consumer

$container['factory'] = function ($c) {
    $settings = $c['settings'];

    return new \Enqueue\Redis\RedisConnectionFactory([
        'vendor' => 'predis',
        'host' => $settings['redis']['host'],
        'port' => $settings['redis']['port'],
        'password' => $settings['redis']['password'],
    ]);
};

// Eloquent

$container->register(new App\Services\EloquentServiceProvider);

// Worker: SendMail

$context = $container['factory']->createContext();
$producer = $context->createProducer();

$emails = $context->createQueue('emails');
$failedJobs = $context->createQueue('failed_jobs');

$consumer = $context->createConsumer($emails);

while(true) {
    $message = $consumer->receive();

    if ($message) {
        $job = unserialize($message->getBody());

        try {
            echo 'sending mail...';
            $job->handle($container['mailer']);
            echo ' sent!' . PHP_EOL;
        } catch (\Exception $e) {
            echo ' error!' . PHP_EOL;
            $producer->send($failedJobs, $message);
        }
    } else {
        echo 'waiting...' . PHP_EOL;
    }

    sleep(5);
}