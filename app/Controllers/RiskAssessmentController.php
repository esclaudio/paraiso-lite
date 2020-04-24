<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskAssessmentValidator;
use App\Models\RiskMatrix;
use App\Models\RiskLikelihood;
use App\Models\RiskConsequence;
use App\Models\RiskAssessment;
use App\Models\Risk;

class RiskAssessmentController extends Controller
{
    /**
     * Create
     *
     * @param  \Slim\Http\Request $request
     * @param  \Slim\Http\Response $response
     * @param  array $args
     *
     * @return \Slim\Http\Response
     * @throws \App\Exceptions\AuthorizationException
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        $risk = Risk::findOrFail($args['risk']);

        $this->authorize('create_assessments', $risk);

        $likelihoods = RiskLikelihood::ofType($risk->type)
            ->orderBy('value')
            ->get()
            ->toArray();

        $consequences = RiskConsequence::ofType($risk->type)
            ->orderBy('value')
            ->get()
            ->toArray();

        $matrix = RiskMatrix::with('level')
            ->ofType($risk->type)
            ->get();

        $values = [];

        foreach($matrix as $value) {
            $level = $value->level;

            $values[$value->code] = [
                'name'        => $level->name,
                'description' => $level->description,
                'color'       => $level->color,
            ];
        }

        return $this->render($response, "risk_assessment/create.twig", [
            'risk'          => $risk,
            'likelihoods'   => $likelihoods,
            'consequences'  => $consequences,
            'values'        => $values,
        ]);
    }

    /**
     * Store
     *
     * @param  \Slim\Http\Request $request
     * @param  \Slim\Http\Response $response
     * @param  array $args
     *
     * @return \Slim\Http\Response
     * @throws \App\Exceptions\AuthorizationException
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $risk = Risk::findOrFail($args['risk']);

        $this->authorize('create_assessments', $risk);

        $attributes = RiskAssessmentValidator::validate($request);

        $assessment = new RiskAssessment($attributes);
        
        /** @var \Illuminate\Database\Connection $db */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $risk->assessments()->save($assessment);
            $risk->tasks()->update(['risk_assessment_id' => $assessment->id]);

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        return $this->redirect($request, $response, 'risk.view', ['risk' => $risk->id]);
    }

    /**
     * Delete
     *
     * @param  \Slim\Http\Request $request
     * @param  \Slim\Http\Response $response
     * @param  array $args
     *
     * @return \Slim\Http\Response
     * @throws \App\Exceptions\AuthorizationException
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $assessment = RiskAssessment::where([
            ['id', $args['assessment']],
            ['risk_id', $args['risk']],
        ])->firstOrFail();

        $this->authorize('delete', $assessment);

        $assessment->delete();

        return $this->redirect($request, $response, 'risk.view', ['risk' => $assessment->risk_id]);
    }
}
