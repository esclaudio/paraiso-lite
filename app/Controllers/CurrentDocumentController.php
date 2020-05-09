<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Filters\CurrentDocumentFilter;

class CurrentDocumentController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $filter = new CurrentDocumentFilter($request);
        
        return $this->render(
            $response,
            'current_document.index',
            [
                'filter' => $filter,
                'documents' => $filter->paginate(5),
            ]
        );
    }
}
