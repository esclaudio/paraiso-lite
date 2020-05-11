<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\DocumentVersionValidator;
use App\Models\DocumentVersion;
use App\Models\DocumentStatus;
use App\Models\Document;
use App\Support\Facades\Storage;

class DocumentVersionController extends Controller
{
    /**
     * Create
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\Document $document */
        $document = Document::unlocked()
            ->where('id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $document);

        return $this->render(
            $response,
            'documents_versions.create',
            [
                'document'        => $document,
                'current_version' => $document->getCurrentVersion(),
                'next_version'    => $document->getNextVersion(),
            ]
        );
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\Document $document */
        $document = Document::unlocked()
            ->where('id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $document);

        $attributes = DocumentVersionValidator::validate($request);

        $version = new DocumentVersion($attributes);
        $document->versions()->save($version);

        if ($attributes['file']) {
            $version->uploadFile($attributes['file']);
        }

        return $this->redirect($request, $response, 'documents.show', ['document' => $document->id]);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion $version */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $version);

        return $this->render(
            $response,
            'documents_versions.edit',
            [
                'version'  => $version,
                'document' => $version->document
            ]
        );
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion $version */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $version);

        $attributes = DocumentVersionValidator::validate($request);

        $version->fill($attributes);
        $version->save();

        if ($attributes['file']) {
            $version->uploadFile($attributes['file']);
        }

        return $this->redirect($request, $response, 'documents.show', ['document' => $version->document_id]);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion $version */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('delete', $version);

        try {
            $version->document->unlock();
            $version->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);
        }

        return $this->redirect(
            $request,
            $response,
            'documents.show',
            [
                'document' => $version->document_id
            ]
        );
    }

    /**
     * Preview
     */
    public function preview(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();
        
        return $this->render($response, 'documents_versions.preview', ['version' => $version]);
    }
}
