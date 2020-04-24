<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\SystemValidator;
use App\Models\System;
use App\Support\Datatable\Datatable;

class SystemController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('systems.show');

        return $this->render($response, 'systems.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('systems.show');

        $system = System::findOrFail($args['system']);

        return $this->render($response, 'systems.show', compact('system'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('systems.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('systems.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $system = System::findOrFail($args['system']);

        $this->authorize('edit', $system);

        return $this->createOrEdit($request, $response, $system);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $system = System::findOrFail($args['system']);

        $this->authorize('edit', $system);

        return $this->storeOrUpdate($request, $response, $system);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $system = System::findOrFail($args['system']);

        $this->authorize('delete', $system);

        try {
            $system->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'systems.show',
                [
                    'system' => $system->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $system->name));
        return $this->redirect($request, $response, 'systems.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('systems.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = System::select([
            'systems.id',
            'systems.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('systems.show', ['system' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, System $system = null): Response
    {
        return $this->render(
            $response,
            'systems.'.($system? 'edit': 'create'),
            [
                'system' => $system
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, System $system = null): Response
    {
        $attributes = SystemValidator::validate($request);

        if ($system) {
            $system->fill($attributes);
        } else {
            $system = new System($attributes);
        }

        $system->save();

        return $this->redirect($request, $response, 'systems.show', ['system' => $system->id]);
    }
}
