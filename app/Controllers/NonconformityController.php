<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\NonconformityValidator;
use App\Models\System;
use App\Models\Process;
use App\Models\Nonconformity;
use App\Support\Datatable\Datatable;

class NonconformityController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('nonconformities.show');

        return $this->render($response, 'nonconformities.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('nonconformities.show');

        $nonconformity = Nonconformity::findOrFail($args['nonconformity']);

        return $this->render($response, 'nonconformities.show', compact('nonconformity'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('nonconformities.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('nonconformities.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $nonconformity = Nonconformity::findOrFail($args['nonconformity']);

        $this->authorize('edit', $nonconformity);

        return $this->createOrEdit($request, $response, $nonconformity);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $nonconformity = Nonconformity::findOrFail($args['nonconformity']);

        $this->authorize('edit', $nonconformity);

        return $this->storeOrUpdate($request, $response, $nonconformity);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $nonconformity = Nonconformity::findOrFail($args['nonconformity']);

        $this->authorize('delete', $nonconformity);

        try {
            $nonconformity->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'nonconformities.show',
                [
                    'nonconformity' => $nonconformity->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $nonconformity->code));
        return $this->redirect($request, $response, 'nonconformities.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('nonconformities.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Nonconformity::select([
            'nonconformities.id',
            'nonconformities.description',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('nonconformities.show', ['nonconformity' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Nonconformity $nonconformity = null): Response
    {
        $systems = System::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $processes = Process::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $this->render(
            $response,
            'nonconformities.'.($nonconformity? 'edit': 'create'),
            [
                'nonconformity' => $nonconformity,
                'systems'       => $systems,
                'processes'     => $processes
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Nonconformity $nonconformity = null): Response
    {
        $attributes = NonconformityValidator::validate($request);

        if ($nonconformity) {
            $nonconformity->fill($attributes);
        } else {
            $nonconformity = new Nonconformity($attributes);
        }

        $nonconformity->save();

        return $this->redirect($request, $response, 'nonconformities.show', ['nonconformity' => $nonconformity->id]);
    }
}
