<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskLevelValidator;
use App\Models\RiskMatrix;
use App\Models\RiskLevel;
use App\Datatables\Datatable;

class RiskLevelController extends Controller
{
    /**
     * Index
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $this->authorize('risk_level.view');

        return $this->render($response, 'risk_level/index.twig');
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
        $this->authorize('risk_level.create');

        return $this->createOrEdit($request, $response);
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
        $this->authorize('risk_level.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * View
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $this->authorize('risk_level.view');

        $level = RiskLevel::findOrFail($args['level']);

        return $this->render($response, 'risk_level/view.twig', compact('level'));
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
        $level = RiskLevel::findOrFail($args['level']);

        $this->authorize('edit', $level);

        return $this->createOrEdit($request, $response, $level);
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
        $level = RiskLevel::findOrFail($args['level']);

        $this->authorize('edit', $level);

        return $this->storeOrUpdate($request, $response, $level);
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
        $level = RiskLevel::findOrFail($args['level']);

        $this->authorize('delete', $level);

        try {
            $level->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'risk_level.view',
                [
                    'risk_level' => $level->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_UI_RISK_LEVEL_DELETED, $level->name));
        return $this->redirect($request, $response, 'risk_level.index');
    }

    /**
     * Datatable
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('risk_level.view');

        $router = $this->router;

        $datatable = new Datatable('risk_level', $this->get('pdo'), $request->getParams());
        $datatable->addRowId()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('description')
            ->addColumn('color')
            ->addColumn('id', 'action', function ($data, $row) use ($router) {
                $viewUrl = $router->pathFor('risk_level.view', ['level' => $data]);

                return "
                    <div class=\"pull-right\">
                        <div class=\"btn-toolbar\" role=\"toolbar\">
                            <a class=\"btn btn-secondary\" href=\"{$viewUrl}\">
                                " . MSG_UI_VIEW . "
                            </a>
                        </div>
                    </div>
                ";
            });

        return $response->withJson($datatable->toArray());
    }

    /**
     * Calculate
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function calculate(Request $request, Response $response, array $args): Response
    {
        $matrix = RiskMatrix::where([
            ['risk_type_id', $request->getParam('risk_type_id')],
            ['risk_likelihood_id', $request->getParam('risk_likelihood_id')],
            ['risk_consequence_id', $request->getParam('risk_consequence_id')],
        ])->firstOrFail();

        return $response->withJson([
            'data' => $matrix->level,
        ]);
    }

    /**
     * Create or edit
     *
     * @param  \Slim\Http\Request         $request
     * @param  \Slim\Http\Response        $response
     * @param  \App\Models\RiskLevel|null $level
     *
     * @return \Slim\Http\Response
     */
    private function createOrEdit(Request $request, Response $response, RiskLevel $level = null): Response
    {
        return $this->render(
            $response,
            'risk_level/'.($level? 'edit.twig': 'create.twig'),
            compact('level')
        );
    }

    /**
     * Store or update
     *
     * @param  \Slim\Http\Request         $request
     * @param  \Slim\Http\Response        $response
     * @param  \App\Models\RiskLevel|null $level
     *
     * @return \Slim\Http\Response
     */
    private function storeOrUpdate(Request $request, Response $response, RiskLevel $level = null): Response
    {
        $attributes = RiskLevelValidator::validate($request);
        
        if ($level) {
            $level->fill($attributes);
        } else {
            $level = new RiskLevel($attributes);
        }

        $level->save();

        return $this->redirect($request, $response, 'risk_level.view', ['level' => $level->id]);
    }
}
