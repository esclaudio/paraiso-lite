<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskLikelihoodValidator;
use App\Models\RiskType;
use App\Models\RiskLikelihood;

class RiskLikelihoodController extends Controller
{
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
        $type = RiskType::findOrFail($args['type']);

        $this->authorize('edit', $type);

        return $this->createOrEdit($request, $response, $type);
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
        $type = RiskType::findOrFail($args['type']);

        $this->authorize('edit', $type);

        return $this->storeOrUpdate($request, $response, $type);
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
        $likelihood = RiskLikelihood::where([
            ['id', $args['likelihood']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $likelihood->type;
        
        $this->authorize('edit', $type);

        return $this->createOrEdit($request, $response, $type, $likelihood);
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
        $likelihood = RiskLikelihood::where([
            ['id', $args['likelihood']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $likelihood->type;
        
        $this->authorize('edit', $type);

        return $this->storeOrUpdate($request, $response, $type, $likelihood);
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
        $likelihood = RiskLikelihood::where([
            ['id', $args['likelihood']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $likelihood->type;

        $this->authorize('edit', $type);

        try {
            $likelihood->delete();
            $this->flash->addMessage('success', sprintf(MSG_UI_RISK_LIKELIHOOD_DELETED, $likelihood->name));
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);
        }
        
        return $this->redirect(
            $request,
            $response,
            'risk_type.view',
            [
                'type' => $likelihood->risk_type_id,
            ]
        );
    }

    /**
     * Select
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function select(Request $request, Response $response, array $args): Response
    {
        $likelihoods = RiskLikelihood::orderBy('value');

        if ($type = $request->getParam('risk_type_id')) {
            $likelihoods->where('risk_type_id', $type);
        }

        return $response->withJson([
            'data' => $likelihoods->get(),
        ]);
    }

    /**
     * Create or edit
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  \App\Models\RiskType $type
     * @param  \App\Models\RiskLikelihood|null $likelihood
     *
     * @return \Slim\Http\Response
     */
    private function createOrEdit(Request $request, Response $response, RiskType $type, RiskLikelihood $likelihood = null): Response
    {
        return $this->render(
            $response,
            'risk_likelihood/'.($likelihood? 'edit.twig': 'create.twig'),
            [
                'type' => $type,
                'likelihood' => $likelihood
            ]
        );
    }

    /**
     * Store or update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  \App\Models\RiskType $type
     * @param  \App\Models\RiskLikelihood|null $likelihood
     *
     * @return \Slim\Http\Response
     */
    private function storeOrUpdate(Request $request, Response $response, RiskType $type, RiskLikelihood $likelihood = null): Response
    {
        $attributes = RiskLikelihoodValidator::validate($request);
        
        if ($likelihood) {
            $likelihood->fill($attributes);
        } else {
            $likelihood = new RiskLikelihood($attributes);
        }

        $type->likelihoods()->save($likelihood);

        return $this->redirect($request, $response, 'risk_type.view', ['type' => $type->id]);
    }
}
