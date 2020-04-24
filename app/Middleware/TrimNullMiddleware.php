<?php

namespace App\Middleware;

use Slim\Http\{
    Request,
    Response
};

class TrimNullMiddleware
{
    protected $except = [
        //
    ];

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $params = $request->getQueryParams();

        if ($params) {
            $request = $request->withQueryParams($this->clean($params));
        }

        $params = $request->getParsedBody();

        if (is_array($params)) {
            $request = $request->withParsedBody($this->clean($params));
        }

        $response = $next($request, $response);
        return $response;
    }

    private function clean(array $params) : array
    {
        foreach($params as $key => $value) {
            if (!in_array($key, $this->except)) {
                if (is_array($value)) {
                    $params[$key] = $this->clean($value);
                } elseif (is_string($value)) {
                    $params[$key] = trim($value) === '' ? null : trim($value);
                }
            }
        }

        return $params;
    }
}
