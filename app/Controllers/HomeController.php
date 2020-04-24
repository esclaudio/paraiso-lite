<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'home');
    }
}
