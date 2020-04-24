<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;

class AlertController extends Controller
{
    /**
     * Invoke
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $response->withJson($this->getAlerts());
    }

    private function getAlerts(): array
    {
        return [];
    }
}