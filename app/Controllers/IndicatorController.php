<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\IndicatorValidator;
use App\Models\User;
use App\Models\System;
use App\Models\Process;
use App\Models\Indicator;
use App\Models\FrequencyType;
use App\Support\Datatable\Datatable;

class IndicatorController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('indicators.show');

        return $this->render($response, 'indicators.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('indicators.show');

        $indicator = Indicator::findOrFail($args['indicator']);

        return $this->render($response, 'indicators.show', compact('indicator'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('indicators.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('indicators.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $indicator = Indicator::findOrFail($args['indicator']);

        $this->authorize('edit', $indicator);

        return $this->createOrEdit($request, $response, $indicator);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $indicator = Indicator::findOrFail($args['indicator']);

        $this->authorize('edit', $indicator);

        return $this->storeOrUpdate($request, $response, $indicator);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $indicator = Indicator::findOrFail($args['indicator']);

        $this->authorize('delete', $indicator);

        try {
            $indicator->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'indicators.show',
                [
                    'indicator' => $indicator->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $indicator->name));
        return $this->redirect($request, $response, 'indicators.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('indicators.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Indicator::select([
            'indicators.id',
            'indicators.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('indicators.show', ['indicator' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Indicator $indicator = null): Response
    {
        $systems = System::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $processes = Process::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $users = User::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        
        $frequencies = FrequencyType::all();

        $decimals = [
            '0' => trans('None'),
            '1' => '0,0',
            '2' => '0,00',
            '3' => '0,000',
            '4' => '0,0000',
            '5' => '0,00000'
        ];

        return $this->render(
            $response,
            'indicators.'.($indicator? 'edit': 'create'),
            [
                'indicator'   => $indicator,
                'systems'     => $systems,
                'processes'   => $processes,
                'users'       => $users,
                'frequencies' => $frequencies,
                'decimals'    => $decimals,
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Indicator $indicator = null): Response
    {
        $attributes = IndicatorValidator::validate($request);

        if ($indicator) {
            $indicator->fill($attributes);
        } else {
            $indicator = new Indicator($attributes);
        }

        $indicator->save();

        return $this->redirect($request, $response, 'indicators.show', ['indicator' => $indicator->id]);
    }
}
