<?php

namespace App\Middleware;

use Slim\{
    Http\Request,
    Http\Response,
    Route
};

class ParamsPersistenceMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        /** @var Slim\Http\Route */
        $route = $request->getAttribute('route');

        $key = sprintf('params_%s', $route->getName());
        $params = $request->getParams();

        if (count($params) === 0 && isset($_SESSION[$key])) {
            return $response->withRedirect($_SESSION[$key]);
        }

        if (count($params)) {
            $_SESSION[$key] = (string)$request->getUri();
        }

        $response = $next($request, $response);
        return $response;
    }
}
