<?php

namespace App\Middleware;

use Slim\Router;
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Container;
use Dflydev\FigCookies\FigRequestCookies;
use App\Support\Facades\Auth;

class AuthMiddleware
{
    /** 
     * Router
     * 
     * @var \Slim\Http\Router 
     */
    protected $router;

    public function __construct(Container $container)
    {
        $this->router = $container->get('router');
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $routeName = $request->getAttribute('route')->getName();

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->password_is_expired && ! in_array($routeName, ['change_password', 'logout'])) {
                if ($request->isXhr()) {
                    return $response->withJson([], 403);
                }

                return $response->withRedirect($this->router->pathFor('change_password'));
            }

            if ($routeName == 'login') {
                if ($request->isXhr()) {
                    return $response->withJson([], 403);
                }

                return $response->withRedirect($this->router->pathFor('home'));
            }
        } else {
            $rememberCookie = FigRequestCookies::get($request, Auth::getRememberCookieName())->getValue();

            if ($rememberCookie && Auth::attemptRemember($rememberCookie)) {
                $response = $next($request, $response);
                return $response;
            }

            if ($routeName != 'login') {
                Auth::setRedirectUrl((string)$request->getUri());
                return $response->withRedirect($this->router->pathFor('login'));
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
