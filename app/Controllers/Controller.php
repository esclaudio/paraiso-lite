<?php

namespace App\Controllers;

use Slim\Http\Stream;
use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;
use App\Models\User;
use App\Mailer\Contracts\MailableContract;
use App\Facades\Storage;
use App\Facades\Mail;

abstract class Controller
{
    /** 
     * Container
     * 
     * @var Psr\Container\ContainerInterface 
     * */
    protected $container;

    /** 
     * User
     * 
     * @var \App\Models\User 
     */
    protected $user;

    /** 
     * View
     * 
     * @var \Slim\Views\Twig 
     */
    protected $view;

    /** 
     * Router
     * 
     * @var \Slim\Router 
     */
    protected $router;

    /** 
     * Logger
     * 
     * @var \Monolog\Logger 
     */
    protected $logger;

    /** 
     * Flash
     * 
     * @var \Slim\Flash\Messages
     */
    protected $flash;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view      = $container->view;
        $this->router    = $container->router;
        $this->logger    = $container->logger;
        $this->user      = $container->auth->user();
        $this->flash     = $container->flash;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Get from container
     * 
     * @param string $name Name
     * 
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * Redirect To
     *
     * @param \Slim\Http\Request  $request     Request
     * @param \Slim\Http\Response $response    Response
     * @param string              $route       Route name
     * @param array               $data        Data
     * @param array               $queryParams Query Params
     * 
     * @return Slim\Http\Response
     */
    protected function redirect(Request $request, Response $response, string $route, array $data = [], array $queryParams = []): Response
    {
        $url = $this->router->pathFor($route, $data, $queryParams);
        
        if ($request->isXhr()) {
            return $response->withJson([
                'redirect' => $url,
            ]);
        }

        return $response->withRedirect($url);
    }

    /**
     * Render
     * 
     * @param Response $response Response
     * @param string   $template Template
     * @param array    $args     Arguments
     * 
     * @return Response
     */
    protected function render(Response $response, string $template, array $args = []): Response
    {
        return $this->view->render($response, str_replace('.', '/', $template).'.twig', $args);
    }

    /**
     * Shows not found page
     * 
     * @param \Slim\Http\Request  $request  Request
     * @param \Slim\Http\Response $response Response
     * 
     * @return Response
     */
    protected function notFound(Request $request, Response $response): Response
    {
        if ($request->isXhr()) {
            return $response->withStatus(404);
        }

        return $this->view->render($response, 'layouts/not_found.twig');
    }

    /**
     * Authorize a given action for the current user
     * 
     * @param string $ability Ability
     * @param [type] $model   Model
     * 
     * @return void
     * 
     * @throws \App\Exceptions\AuthorizationException
     */
    protected function authorize(string $ability, $model = null)
    {
        if ($this->user->cannot($ability, $model)) {
            throw new \App\Exceptions\AuthorizationException;
        }
    }

    /**
     * Returns the path for a route name
     * 
     * @param string $route       Route name
     * @param array  $args        Arguments
     * @param array  $queryParams Query params
     * 
     * @return string
     */
    protected function pathFor(string $route, array $args = [], array $queryParams = []) : string
    {
        return $this->router->pathFor($route, $args, $queryParams);
    }

    /**
     * Send mail
     * 
     * @param string|\App\Models\User|\Illuminate\Support\Collection $user User
     * @param \App\Mailer\Contracts\MailableContract $mailable Mailable
     * 
     * @return void
     */
    protected function sendMail($users, MailableContract $mailable)
    {
        try {
            if (is_string($users)) {
                Mail::to($users)->send($mailable);
            } elseif ($users instanceof \App\Models\User) {
                $users->send($mailable);
            } elseif ($users instanceof \Illuminate\Support\Collection) {
                $users = $users
                    ->filter(function ($user) {
                        return filter_var($user->email, FILTER_VALIDATE_EMAIL);
                    })
                    ->pluck('name', 'email')
                    ->toArray();

                Mail::to($users)->send($mailable);
            }
        } catch (\Throwable $t) {
            $this->logger->error(
                $t->getMessage(),
                compact(
                    'users',
                    'mailable'
                )
            );
        }
    }

    /**
     * Returns a response with inline file
     * 
     * @param \Slim\Http\Request  $request  Request
     * @param \Slim\Http\Response $response Response
     * @param string              $path     File path
     * @param string              $rename   File name (optional)
     * 
     * @return \Slim\Http\Response
     */
    protected function responseInline(Request $request, Response $response, string $path, string $rename = null): Response
    {
        // if (!file_exists($path)) {
        //     return $this->notFound($request, $response);
        // }

        if (!$rename) {
            $rename = pathinfo($path, PATHINFO_BASENAME);
        }

        // $stream = Storage::readStream($path);
        // $mimetype = Storage::getMimetype($path);

        // $stream = new Stream(fopen($path, 'rb'));

        return $response->withHeader('Content-Type', $mimetype)
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'inline; filename="' . $rename . '"')
            ->withHeader('Cache-Control', 'max-age=60, public')
            ->withBody(new Stream($stream));
    }

    /**
     * Returns a response with download file
     * 
     * @param \Slim\Http\Request  $request  Request
     * @param \Slim\Http\Response $response Response
     * @param string              $path     File path
     * @param string              $rename   File name (optional)
     * 
     * @return \Slim\Http\Response
     */
    protected function responseDownload(Request $request, Response $response, string $path, string $rename = null): Response
    {
        if (!file_exists($path)) {
            return $this->notFound($request, $response);
        }

        if (!$rename) {
            $rename = pathinfo($path, PATHINFO_BASENAME);
        }

        $stream = new Stream(fopen($path, 'rb'));

        return $response->withHeader('Content-Type', mime_content_type($path))
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $rename . '"')
            ->withHeader('Cache-Control', 'max-age=60, public')
            ->withBody($stream);
    }
}
