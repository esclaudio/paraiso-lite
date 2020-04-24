<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskConsequenceValidator;
use App\Models\RiskType;
use App\Models\RiskConsequence;

class RiskConsequenceController extends Controller
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
        $consequence = RiskConsequence::where([
            ['id', $args['consequence']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $consequence->type;
        
        $this->authorize('edit', $type);

        return $this->createOrEdit($request, $response, $type, $consequence);
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
        $consequence = RiskConsequence::where([
            ['id', $args['consequence']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $consequence->type;
        
        $this->authorize('edit', $type);

        return $this->storeOrUpdate($request, $response, $type, $consequence);
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
        $consequence = RiskConsequence::where([
            ['id', $args['consequence']],
            ['risk_type_id', $args['type']],
        ])->firstOrFail();

        $type = $consequence->type;

        $this->authorize('edit', $type);

        try {
            $consequence->delete();
            $this->flash->addMessage('success', sprintf(MSG_UI_RISK_CONSEQUENCE_DELETED, $consequence->name));
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);
        }
        
        return $this->redirect(
            $request,
            $response,
            'risk_type.view',
            [
                'type' => $consequence->risk_type_id,
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
        $consequences = RiskConsequence::orderBy('value');

        if ($type = $request->getParam('risk_type_id')) {
            $consequences->where('risk_type_id', $type);
        }

        return $response->withJson([
            'data' => $consequences->get(),
        ]);
    }

    /**
     * Create or edit
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  \App\Models\RiskType $type
     * @param  \App\Models\RiskConsequence|null $consequence
     *
     * @return \Slim\Http\Response
     */
    private function createOrEdit(Request $request, Response $response, RiskType $type, RiskConsequence $consequence = null): Response
    {
        return $this->render(
            $response,
            'risk_consequence/'.($consequence? 'edit.twig': 'create.twig'),
            [
                'type' => $type,
                'consequence' => $consequence
            ]
        );
    }

    /**
     * Store or update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  \App\Models\RiskType $type
     * @param  \App\Models\RiskConsequence|null $consequence
     *
     * @return \Slim\Http\Response
     */
    private function storeOrUpdate(Request $request, Response $response, RiskType $type, RiskConsequence $consequence = null): Response
    {
        $attributes = RiskConsequenceValidator::validate($request);

        if ($consequence) {
            $consequence->fill($attributes);
        } else {
            $consequence = new RiskConsequence($attributes);
        }

        $type->consequences()->save($consequence);

        return $this->redirect($request, $response, 'risk_type.view', ['type' => $type->id]);
    }
}
