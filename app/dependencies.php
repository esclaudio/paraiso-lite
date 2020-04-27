<?php

// Twig
$container['view'] = function ($c) {
    $settings = $c['settings'];

    $view = new Slim\Views\Twig($settings['view']['templates'], [
        'cache' => $settings['view']['cache'],
        'debug' => $settings['view']['debug'],
    ]);

    // Extensions

    $request = $c->get('request');

    $view->addExtension(new Slim\Views\TwigExtension(
        $c->get('router'),
        $request->getUri()
    ));
    $view->addExtension(new App\TwigExtensions\CsrfExtension(
        $c->get('csrf')
    ));
    $view->addExtension(new App\TwigExtensions\VersionExtension(
        ROOT . '/public/manifest.json'
    ));
    $view->addExtension(new App\TwigExtensions\DebugExtension);
    $view->addExtension(new App\TwigExtensions\TranslationExtension($c->get('translator')));
    
    // Globals

    $env = $view->getEnvironment();
    
    $env->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat(DATE_FORMAT, '%d days');

    $env->addGlobal('app_name', $settings['app_name']);
    $env->addGlobal('help_url', $settings['help_url']);
    $env->addGlobal('flash', $c->get('flash'));
    $env->addGlobal('menu', $c->get('menu'));
    $env->addGlobal('csrf', $c->get('csrf'));

    $auth = $c->get('auth');
    $env->addGlobal('auth', [
        'check' => $auth->check(),
        'user' => $auth->user(),
    ]);

    return $view;
};

// Menu
$container['menu'] = function ($c) {
    $user = $c->get('auth')->user();

    if (is_null($user)) {
        return [];
    }

    $menu = [];

    // Each menu file is a section of the menu

    foreach (glob(ROOT . '/menu/*.php') as $file) {
        $section = require $file;
        $title = $section['title'];
        
        // Each section has one or more groups of items

        foreach ($section['groups'] as $group) {

            // Filter menu by permissions
            
            $items = array_filter($group, function ($item) use ($user) {
                return ! isset($item['permission']) || $user->can($item['permission']);
            });

            if ($items) {
                $menu[$title][] = $items;
            }
        }
    }
    
    return $menu;
};

// CSRF
$container['csrf'] = function ($c) {
    $guard = new Slim\Csrf\Guard;
    $guard->setPersistentTokenMode(true);
    $guard->setFailureCallable(function ($request, $response, $next) {
        if ($request->isXhr()) {
            return $response->withJson([
                'error' => MSG_ERROR_TOKEN
            ], 400);
        }

        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });

    return $guard;
};

// Database (PDO)
$container['pdo'] = function ($c) {
    $settings = $c['settings']['database'];

    if ($settings['driver'] === 'sqlite') {
        return new PDO('sqlite:' . $settings['database'], null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    if ($settings['driver'] === 'mysql') {
        return new PDO('mysql:host=' . $settings['host'] . ';dbname=' . $settings['database'],
            $settings['username'],
            $settings['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $settings['charset']
            ]
        );
    }

    if ($settings['driver'] === 'pgsql') {
        return new PDO('pgsql:host=' . $settings['host'] . ';dbname=' . $settings['database'],
            $settings['username'],
            $settings['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    return null;
};

// Flash messages
$container['flash'] = function ($c) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return new Slim\Flash\Messages;
    }

    return null;
};

// Auth
$container['auth'] = function ($c) {
    return new App\Auth\Auth;
};

// Intervention (IMagick)
$container['image'] = function ($c) {
    return new Intervention\Image\ImageManager([
        'driver' => 'imagick'
    ]);
};

// Unoconv
$container['unoconv'] = function ($c) {
    $settings = $c['settings'];

    return new App\Unoconv\Unoconv(
        $settings['unoconv']['bin'],
        $c->get('logger')
    );
};

// Mail
$container['mailer'] = function ($c) {
    $settings = $c['settings'];

    if (empty($settings['mail']['host'])) {
        return null;
    }

    $transport = new Swift_SmtpTransport(
        $settings['mail']['host'],
        $settings['mail']['port'],
        $settings['mail']['security']
    );

    $transport->setUsername($settings['mail']['username']);
    $transport->setPassword($settings['mail']['password']);

    // Check if authentication has failed
    // try {
    //     $transport->start();
    // } catch (\Throwable $t) {
    //     $c['logger']->error($t->getMessage(), compact('transport'));
    //     return null;
    // }

    $view = new Slim\Views\Twig($settings['view']['templates']);
    $view->addExtension(new Slim\Views\TwigExtension(
        $c->get('router'),
        $settings['app_url']
    ));

    $queue = new \App\Queue\RedisQueue([
        'vendor' => 'predis',
        'host' => $settings['redis']['host'],
        'port' => $settings['redis']['port'],
        'password' => $settings['redis']['password'],
    ], 'emails');

    $mailer = new App\Mailer\Mailer(
        new Swift_Mailer($transport),
        $view,
        $queue
    );

    $mailer->alwaysFrom($settings['mail']['from']['address'], $settings['mail']['from']['name']);

    return $mailer;
};

// Monolog
$container['logger'] = function ($c) {
    $logger = new Monolog\Logger('app');

    $handler = new Monolog\Handler\StreamHandler(
        sprintf('%s/%s.log', LOGS_PATH, date('Ymd'))
    );

    $logger->pushHandler($handler);

    return $logger;
};

// PDF
$container['pdf'] = function ($c) {
    $settings = $c['settings'];

    return new mikehaertl\wkhtmlto\Pdf([
        'commandOptions' => [
            'useExec' => true,
        ],
        'encoding'      => 'UTF-8',
        'no-outline',   // Make Chrome not complain
        'page-size'     => 'A4',
        'orientation'   => 'Portrait',
        'margin-top'    => 4,
        'margin-right'  => 4,
        'margin-bottom' => 4,
        'margin-left'   => 4,
        'disable-smart-shrinking',
        'zoom'          => $settings['wkhtml']['zoom'], // Soluciona problemas con el tamaño de letra en servidores Linux
    ]);
};

// Error handler
$container['errorHandler'] = function ($c) {
    $settings = $c['settings'];

    return new App\Handlers\ErrorHandler($c['logger'], $c['view'], $settings['displayErrorDetails']);
};

// Translator
$container['translator'] = function ($c) {
    /** @var \App\Auth\Auth */
    $auth = $c->get('auth');
    $locale = $auth->user()->language ?? App\Models\Language::EN;

    $translator = new Symfony\Component\Translation\Translator($locale);
    $translator->setFallbackLocales([App\Models\Language::EN]);
    $translator->addLoader('mo', new Symfony\Component\Translation\Loader\MoFileLoader);
    $translator->addLoader('array', new Symfony\Component\Translation\Loader\ArrayLoader);

    // Global domain
    foreach (glob(ROOT . '/resources/lang/*.mo') as $file) {
        $locale = basename($file, '.mo');
        $translator->addResource('mo', $file, $locale);
    }

    // Validators domain
    foreach (glob(ROOT . '/resources/lang/validators/*.php') as $file) {
        $locale = basename($file, '.php');
        $messages = require $file;
        $translator->addResource('array', $messages, $locale, 'validators');
    }
    
    return $translator;
};

$container['queue'] = function ($c) {
    $settings = $c['settings']['redis'];
    
    return new \App\Queue\RedisQueue([
        'vendor' => 'predis',
        'host' => $settings['host'],
        'port' => $settings['port'],
        'password' => $settings['password'],
    ]);
};

// Storage
$container['storage'] = function ($c) {
    $settings = $c['settings']['disks'];
    $disks = [];

    foreach ($settings as $disk => $properties) {
        if ($properties['driver'] == 'local') {
            $adapter = new League\Flysystem\Adapter\Local($properties['root']);
        }

        if ($properties['driver'] == 's3') {
            $client = new Aws\S3\S3Client([
                'credentials' => [
                    'key'    => $properties['key'],
                    'secret' => $properties['secret'],
                ],
                'region' => $properties['region'],
                'version' => 'latest',
            ]);

            $adapter = new League\Flysystem\AwsS3v3\AwsS3Adapter($client, $properties['bucket']);
        }

        $disks[$disk] = new App\Support\Filesystem\Filesystem($adapter);
    }

    return new class($disks) {
        private $disks;
        private $defaultDisk;

        public function __construct($disks, $defaultDisk = 'local')
        {
            $this->disks = $disks;
            $this->defaultDisk = $defaultDisk;
        }

        public function disk(string $name): App\Support\Filesystem\Filesystem
        {
            return $this->disks[$name];
        }

        public function __call($method, $args)
        {
            return $this->disks[$this->defaultDisk]->$method(...$args);
        }
    };
};

$container->register(new \App\Services\EloquentServiceProvider);
$container->register(new \App\Services\CacheServiceProvider);

App\Facades\Facade::setFacadeContainer($container);