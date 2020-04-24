<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Models\RiskType;
use App\Models\RiskMatrix;
use App\Models\RiskLevel;

class RiskMatrixController extends Controller
{
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
        $type = RiskType::findOrFail($args['type']);

        $this->authorize('edit', $type);

        $likelihoods = $type->likelihoods()
            ->orderBy('value')
            ->get()
            ->keyBy('id');

        $consequences = $type->consequences()
            ->orderBy('value')
            ->get()
            ->keyBy('id');

        $levels = RiskLevel::orderBy('id')
            ->get()
            ->keyBy('id');

        $matrix = $type->matrix();

        return $this->render(
            $response,
            'risk_matrix/edit.twig',
            [
                'type' => $type,
                'likelihoods' => $likelihoods,
                'consequences' => $consequences,
                'matrix' => $matrix,
                'levels' => $levels,
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
        $this->authorize('risk_matrix.edit');

        $type = RiskType::findOrFail($args['type']);

        $likelihoods = $request->getParam('risk_likelihood_id');
        $consequences = $request->getParam('risk_consequence_id');
        $levels = $request->getParam('risk_level_id');

        /** @var Illuminate\Database\Connection */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            RiskMatrix::where('risk_type_id', $args['type'])->delete();

            foreach($levels as $i => $level) {
                RiskMatrix::create([
                    'risk_type_id' => $type->id,
                    'risk_likelihood_id' => $likelihoods[$i],
                    'risk_consequence_id' => $consequences[$i],
                    'risk_level_id' => $level
                ]);
            }

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();
            throw $t;
        }

        return $this->redirect($request, $response, 'risk_type.view', ['type' => $type->id]);
    }
}
