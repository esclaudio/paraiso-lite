<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskTypeValidator;
use App\Models\RiskType;
use App\Support\Datatable\Datatable;

class RiskTypeController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('risks_types.show');

        return $this->render($response, 'risks_types.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('risks_types.show');

        $riskType = RiskType::findOrFail($args['risk_type']);

        return $this->render($response, 'risks_types.show', ['risk_type' => $riskType]);
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('risks_types.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('risks_types.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $riskType = RiskType::findOrFail($args['risk_type']);

        $this->authorize('edit', $riskType);

        return $this->createOrEdit($request, $response, $riskType);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $riskType = RiskType::findOrFail($args['risk_type']);

        $this->authorize('edit', $riskType);

        return $this->storeOrUpdate($request, $response, $riskType);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $riskType = RiskType::findOrFail($args['risk_type']);

        $this->authorize('delete', $riskType);

        try {
            $riskType->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'risks_types.show',
                [
                    'risk_type' => $riskType->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $riskType->name));
        return $this->redirect($request, $response, 'risks_types.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('risks_types.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = RiskType::select([
            'risks_types.id',
            'risks_types.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('risks_types.show', ['risk_type' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, RiskType $riskType = null): Response
    {
        return $this->render(
            $response,
            'risks_types.'.($riskType? 'edit': 'create'),
            [
                'risk_type' => $riskType
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, RiskType $riskType = null): Response
    {
        $attributes = RiskTypeValidator::validate($request);

        if ($riskType) {
            $riskType->fill($attributes);
        } else {
            $riskType = new RiskType($attributes);
        }

        $riskType->save();

        return $this->redirect($request, $response, 'risks_types.show', ['risk_type' => $riskType->id]);
    }
}
