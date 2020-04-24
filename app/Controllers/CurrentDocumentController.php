<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Models\System;
use App\Models\Process;
use App\Models\DocumentType;
use App\Models\Document;
use App\Filters\CurrentDocumentFilter;

class CurrentDocumentController extends Controller
{
    /**
     * Index
     *
     * @param  Slim\Http\Request  $request
     * @param  Slim\Http\Response $response
     * @return Slim\Http\Response
     */
    public function index(Request $request, Response $response): Response
    {
        $documents = Document::filter($request, CurrentDocumentFilter::class)
            ->with(['system', 'process', 'type'])
            ->orderBy('code')
            ->orderBy('title')
            ->paginateFilter(ITEMS_PER_PAGE);

        $systems = System::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $processes = Process::active()
            ->internal()
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $types = DocumentType::orderBy('description')
            ->pluck('description', 'id')
            ->toArray();

        return $this->render(
            $response,
            'current_document/index.twig',
            [
                'documents' => $documents,
                'processes' => $processes,
                'systems' => $systems,
                'types' => $types,
                'params' => $documents->params(),
            ]
        );
    }
}
