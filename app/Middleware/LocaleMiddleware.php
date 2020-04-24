<?php

namespace App\Middleware;

use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Container;
use App\Facades\Auth;

class LocaleMiddleware
{
    /** 
     * Translator
     * 
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    public function __construct(Container $container)
    {
        $this->translator = $container->get('translator');
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (Auth::check()) {
            $this->translator->setLocale(Auth::user()->language);
        }

        $response = $next($request, $response);
        return $response;
    }
}
