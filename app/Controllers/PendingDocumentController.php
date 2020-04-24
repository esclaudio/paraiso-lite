<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Models\DocumentVersion;

class PendingDocumentController extends Controller
{
    /**
     * Index
     *
     * @param  Slim\Http\Request  $request
     * @param  Slim\Http\Response $response
     * 
     * @return Slim\Http\Response
     */
    public function index(Request $request, Response $response): Response
    {
        if ($this->user->is_admin) {
            $versions = DocumentVersion::pending();
        } else {
            $versions = DocumentVersion::pendingOf($this->user);
        }
        
        $versions = $versions->orderBy('created_at')->get();

        return $this->render($response, 'pending_document/index.twig', compact('versions'));
    }
}
