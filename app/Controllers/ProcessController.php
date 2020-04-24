<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\ProcessValidator;
use App\Models\Process;
use App\Support\Datatable\Datatable;

class ProcessController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('processes.show');

        return $this->render($response, 'processes.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('processes.show');

        $process = Process::findOrFail($args['process']);

        return $this->render($response, 'processes.show', compact('process'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('processes.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('processes.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $process = Process::findOrFail($args['process']);

        $this->authorize('edit', $process);

        return $this->createOrEdit($request, $response, $process);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $process = Process::findOrFail($args['process']);

        $this->authorize('edit', $process);

        return $this->storeOrUpdate($request, $response, $process);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $process = Process::findOrFail($args['process']);

        $this->authorize('delete', $process);

        try {
            $process->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'processes.show',
                [
                    'process' => $process->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $process->name));
        return $this->redirect($request, $response, 'processes.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('processes.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Process::select([
            'processes.id',
            'processes.name',
            'processes.is_active',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('processes.show', ['process' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Process $process = null): Response
    {
        return $this->render(
            $response,
            'processes.'.($process? 'edit': 'create'),
            [
                'process' => $process
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Process $process = null): Response
    {
        $attributes = ProcessValidator::validate($request);

        if ($process) {
            $process->fill($attributes);
        } else {
            $process = new Process($attributes);
        }

        $process->save();

        return $this->redirect($request, $response, 'processes.show', ['process' => $process->id]);
    }
}
