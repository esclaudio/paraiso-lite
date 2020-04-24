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
     * View
     *
     * @param \Slim\Http\Request  $request  Request
     * @param \Slim\Http\Response $response Response
     * @param array               $args     Arguments
     *
     * @return \Slim\Http\Response
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $this->authorize('document.view');

        $version = DocumentVersion::where([
            ['id', $args['version']],
            ['document_id', $args['document']],
        ])->firstOrFail();

        $transitions = $version->transitions()
            ->with('createdBy')
            ->orderByDesc('created_at')
            ->get();
        
        return $this->render(
            $response,
            "document_version/view.twig",
            [
                'document'    => $version->document,
                'version'     => $version,
                'transitions' => $transitions,
            ]
        );
    }

    /**
     * Create
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        $document = Document::where('is_locked', false)
            ->where('id', $args['document'])
            ->firstOrFail();

        $this->authorize('edit', $document);

        return $this->render(
            $response,
            'document_version/create.twig',
            [
                'document'          => $document,
                'published_version' => $document->published_version,
                'next_version'      => $document->next_version,
            ]
        );
    }

    /**
     * Store
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $document = Document::where('id', $args['document'])
            ->where('is_locked', false)
            ->firstOrFail();

        $this->authorize('edit', $document);

        $attributes = DocumentVersionValidator::validate($request);
        
        $uploads = uploads($request);
        
        if ($document->type->require_file && ! isset($uploads['file'])) {
            return $response->withJson([
                'error' => trans('File is required.')
            ]);
        }

        $version = new DocumentVersion;
        $version->fill($attributes);

        if (isset($uploads['file'])) {
            $version->uploadFile($uploads['file']);

            if (isset($uploads['preview'])) {
                $version->uploadPreview($uploads['preview']);
            } else {
                $version->makePreview($this->get('unoconv'));
            }
        }

        $document->versions()->save($version);

        return $this->redirect($request, $response, 'document.view', ['document' => $document->id]);
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
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();
        
        $filename = $version->preview_path;

        if ( ! file_exists($filename)) {
            return $this->notFound($request, $response);
        }

        // If param info is set, return information of the preview

        if ($info = $request->getParam('info')) {
            $data = [
                'name'      => basename($filename),
                'mimetype'  => mime_content_type($filename),
                'size'      => filesize($filename),
                'can_print' => false,
            ];
            
            if (array_key_exists($info, $data)) {
                return $response->withJson([$info => $data[$info]]);
            }

            return $response->withJson($data);
        }

        // Otherwise, download the preview
        
        return $this->responseInline($request, $response, $filename);
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
