<?php

namespace App\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Monolog\Logger;
use Slim\Views\Twig;

final class ErrorHandler extends \Slim\Handlers\Error
{
    /**
     * @var bool
     */
    protected $displayErrorDetails;

    /**
     * Logger
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * View
     * @var Slim\Views\Twig
     */
    protected $view;

    public function __construct(Logger $logger, Twig $view, bool $displayErrorDetails = false)
    {
        $this->logger = $logger;
        $this->view = $view;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        if ($exception instanceof \App\Exceptions\AuthorizationException) {
            if ($request->isXhr()) {
                return $response->withStatus(403);
            }

            return $this->view->render($response, 'layouts/access_denied.twig');
        }

        if ($exception instanceof \App\Exceptions\ValidationException) {
            if ($request->isXhr()) {
                return $response->withJson($exception->errors(), 400);
            }

            return $response; // TODO: Redirect to previous page with flash
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            if ($request->isXhr()) {
                return $response->withStatus(404);
            }

            return $this->view->render($response, 'layouts/not_found.twig');
        }

        $this->logger->error($exception->getMessage(), [
            'exception' => sprintf('Code %s on file %s, line %s', $exception->getCode(), $exception->getFile(), $exception->getLine()),
            'uri' => (string)$request->getUri(),
            'method' => $request->getMethod(),
        ]);

        return parent::__invoke($request, $response, $exception);
    }
}
