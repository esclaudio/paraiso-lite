<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Attachment;
use App\Exceptions\AuthorizationException;
use App\Support\Datatable\Datatable;

class AttachmentController extends Controller
{
    /**
     * Index
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $router = $this->get('router');
        $user = $this->user;

        $attachments = Attachment::ofModel($model)
            ->select([
                'attachment.id',
                'attachment.name',
                'attachment.extension',
                'attachment.type',
                'attachment.is_public',
                'attachment.created_at',
                'attachment.created_by',
                'creator.firstname AS creator_firstname',
                'creator.lastname AS creator_lastname',
            ])
            ->leftJoin('user AS creator', 'attachment.created_by', '=', 'creator.id')
            ->where(function (Builder $query) use ($user) {
                $query->where('is_public', true)->orWhere('created_by', $user->id);
            })
            ->getQuery();

        $datatables = new Datatable($attachments, $request, $response);

        return $datatables
            ->addColumn('download_url', function ($row) use ($router, $args) {
                return $router->pathFor(
                    'attachment.download',
                    [
                        'model_type' => $args['model_type'],
                        'model_id'   => $args['model_id'],
                        'attachment' => $row->id
                    ]
                );
            })
            ->addColumn('delete_url', function ($row) use ($router, $user, $args) {
                if ($user->is_admin || $user->id == $row->created_by) {
                    return $router->pathFor(
                        'attachment.delete',
                        [
                            'model_type' => $args['model_type'],
                            'model_id'   => $args['model_id'],
                            'attachment' => $row->id
                        ]
                    );
                }

                return null;
            })
            ->addColumn('edit_url', function ($row) use ($router, $user, $args) {
                if ($user->is_admin || $user->id == $row->created_by) {
                    return $router->pathFor(
                        'attachment.update',
                        [
                            'model_type' => $args['model_type'],
                            'model_id'   => $args['model_id'],
                            'attachment' => $row->id
                        ]
                    );
                }

                return null;
            })
            ->addColumn('make_public_url', function ($row) use ($router, $user, $args) {
                if ( ! $row->is_public && ($user->is_admin || $user->id == $row->created_by)) {
                    return $router->pathFor(
                        'attachment.make_public',
                        [
                            'model_type' => $args['model_type'],
                            'model_id'   => $args['model_id'],
                            'attachment' => $row->id
                        ]
                    );
                }

                return null;
            })
            ->addColumn('make_private_url', function ($row) use ($router, $user, $args) {
                if ($row->is_public && ($user->is_admin || $user->id == $row->created_by)) {
                    return $router->pathFor(
                        'attachment.make_private',
                        [
                            'model_type' => $args['model_type'],
                            'model_id'   => $args['model_id'],
                            'attachment' => $row->id
                        ]
                    );
                }

                return null;
            })
            ->response();
    }

    /**
     * Store
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $uploadedFiles = $request->getUploadedFiles();

        if ( ! $uploadedFiles) {
            return $response->withJson([]);
        }

        $attachments = [];

        foreach($uploadedFiles as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $attachment = new Attachment;
                $attachment->upload($uploadedFile);

                $attachments[] = $attachment;
            }
        }

        $model->attachments()->saveMany($attachments);

        return $response->withJson($attachments);
    }

    /**
     * Update
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $attachment = Attachment::ofModel($model)
            ->where('id', $args['attachment'])
            ->firstOrFail();

        if ( ! $this->user->is_admin && $this->user->id !== $attachment->created_by) {
            throw new AuthorizationException;
        }

        if ($name = $request->getParam('name')) {
            $attachment->name = $name;
            $attachment->save();
        }

        return $response;
    }

    /**
     * Make public
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function makePublic(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $attachment = Attachment::ofModel($model)
            ->where('id', $args['attachment'])
            ->firstOrFail();

        if ( ! $this->user->is_admin && $this->user->id !== $attachment->created_by) {
            throw new AuthorizationException;
        }

        $attachment->is_public = true;
        $attachment->save();

        return $response;
    }

    /**
     * Make private
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function makePrivate(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $attachment = Attachment::ofModel($model)
            ->where('id', $args['attachment'])
            ->firstOrFail();

        if ( ! $this->user->is_admin && $this->user->id !== $attachment->created_by) {
            throw new AuthorizationException;
        }

        $attachment->is_public = false;
        $attachment->save();

        return $response;
    }

    /**
     * Download
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function download(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);
        $user = $this->user;

        $attachment = Attachment::ofModel($model)
            ->where('id', $args['attachment'])
            ->where(function (Builder $query) use ($user) {
                $query->where('is_public', true)->orWhere('created_by', $user->id);
            })
            ->firstOrFail();

        return $this->responseInline($request, $response, $attachment->path, $attachment->nameWithExtension);
    }

    /**
     * Delete
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $attachment = Attachment::ofModel($model)
            ->where('id', $args['attachment'])
            ->firstOrFail();

        if ( ! $this->user->is_admin && $this->user->id !== $attachment->created_by) {
            throw new AuthorizationException;
        }

        if (file_exists($attachment->path)) {
            unlink($attachment->path);
        }

        $attachment->delete();

        return $response;
    }

    /**
     * Get model
     *
     * @param string $type
     * @param string $id
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getModel(string $type, string $id): Model
    {
        $class = $this->getModelClass($type);

        return $class::findOrFail($id);
    }

    private function getModelClass(string $type): string
    {
        return '\\App\\Models\\' . str_replace('_', '', ucwords($type, '_'));
    }
}
