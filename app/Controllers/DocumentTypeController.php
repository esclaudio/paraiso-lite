<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\DocumentTypeValidator;
use App\Models\DocumentType;
use App\Support\Datatable\Datatable;

class DocumentTypeController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('documents_types.show');

        return $this->render($response, 'documents_types.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('documents_types.show');

        $documentType = DocumentType::findOrFail($args['document_type']);

        return $this->render($response, 'documents_types.show', ['document_type' => $documentType]);
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('documents_types.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('documents_types.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $documentType = DocumentType::findOrFail($args['document_type']);

        $this->authorize('edit', $documentType);

        return $this->createOrEdit($request, $response, $documentType);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $documentType = DocumentType::findOrFail($args['document_type']);

        $this->authorize('edit', $documentType);

        return $this->storeOrUpdate($request, $response, $documentType);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $documentType = DocumentType::findOrFail($args['document_type']);

        $this->authorize('delete', $documentType);

        try {
            $documentType->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'documents_types.show',
                [
                    'document_type' => $documentType->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $documentType->name));
        return $this->redirect($request, $response, 'documents_types.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('documents_types.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = DocumentType::select([
            'documents_types.id',
            'documents_types.name',
            'documents_types.prefix',
            'documents_types.next_number',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('documents_types.show', ['document_type' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, DocumentType $documentType = null): Response
    {
        return $this->render(
            $response,
            'documents_types.'.($documentType? 'edit': 'create'),
            [
                'document_type' => $documentType
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, DocumentType $documentType = null): Response
    {
        $attributes = DocumentTypeValidator::validate($request);

        if ($documentType) {
            $documentType->fill($attributes);
        } else {
            $documentType = new DocumentType($attributes);
        }

        $documentType->save();

        return $this->redirect($request, $response, 'documents_types.show', ['document_type' => $documentType->id]);
    }
}
