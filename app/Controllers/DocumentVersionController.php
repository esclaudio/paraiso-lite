<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\DocumentVersionValidator;
use App\Models\DocumentVersion;
use App\Models\DocumentStatus;
use App\Models\Document;

class DocumentVersionController extends Controller
{
    /**
     * Create
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\Document */
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
        $document = Document::unlocked()
            ->where('id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $document);

        $version = new DocumentVersion(
            DocumentVersionValidator::validate($request)
        );
        
        $uploads = filterUploads($request);
        
        if (isset($uploads['file'])) {
            $version->uploadFile($uploads['file']);
        }

        if (isset($uploads['preview'])) {
            $version->uploadPreview($uploads['preview']);
        }

        $document->versions()->save($version);

        return $this->redirect($request, $response, 'documents.show', ['document' => $document->id]);
    }

    /**
     * Edit
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $version);

        return $this->render(
            $response,
            'document_version/edit.twig',
            [
                'version' => $version,
                'document' => $version->document
            ]
        );
    }

    /**
     * Update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $version);

        $attributes = DocumentVersionValidator::validate($request);
        
        $version->fill($attributes);
        $version->save();
        
        $uploads = uploads($request);
        
        if (isset($uploads['preview'])) {
            $version->uploadPreview($uploads['preview']);
        }

        return $this->redirect($request, $response, 'document.view', ['document' => $version->document_id]);
    }

    /**
     * Delete
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $this->authorize('delete', $version);

        try {
            $version->document->unlock();
            $version->delete();
            
            $this->flash->addMessage('success', sprintf(MSG_UI_DOCUMENT_VERSION_DELETED, $version->version, $version->document->full_title));
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);
        }

        return $this->redirect(
            $request,
            $response,
            'document.view',
            [
                'document' => $version->document->id
            ]
        );
    }

    /**
     * Download
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function download(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        if ( ! $version->file_exists) {
            return $this->notFound($request, $response);
        }

        $name = sprintf('%s v%s_%s.%s',
            $version->document->full_title,
            $version->version,
            date('YmdHis'),
            $version->extension
        );

        return $this->responseDownload($request, $response, $version->file_path, $name);
    }

    /**
     * Preview
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function preview(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();
        
        if ( ! $version->has_preview) {
            return $this->notFound($request, $response);
        }

        return $this->responseInline($request, $response, $version->preview_path);
    }

    /**
     * Periodic review
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function periodicReview(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::published()
            ->where([
                ['id', $args['version']],
                ['document_id', $args['document']],
            ])
            ->firstOrFail();

        $this->authorize('edit', $version->document);

        if ((bool)$request->getParam('need-updating')) {
            return $this->redirect($request, $response, 'document_version.create', ['document' => $version->document_id]);
        }

        $version->reviewed($this->user);

        return $this->redirect($request, $response, 'document.view', ['document' => $version->document_id]);
    }
}
