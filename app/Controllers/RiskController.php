<?php

namespace App\Controllers;

use Throwable;
use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RiskValidator;
use App\Validators\RiskUpdateValidator;
use App\Validators\RiskStoreValidator;
use App\Models\User;
use App\Models\System;
use App\Models\SwotItem;
use App\Models\Source;
use App\Models\RiskType;
use App\Models\RiskTreatmentType;
use App\Models\RiskLevel;
use App\Models\Risk;
use App\Models\Process;
use App\Exports\RiskExporter;
use App\Excel\Excel;

class RiskController extends Controller
{
    /**
     * Index
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     *
     * @return \Slim\Htpp\Response
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('risk.view');
        
        $risks = Risk::filter($request)
            ->with('type', 'system', 'process', 'level', 'lastAssessment')
            ->orderBy('id', 'desc')
            ->paginateFilter(ITEMS_PER_PAGE);

        $systems = System::orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $processes = Process::active()
            ->internal()
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $sources = Source::orderBy('description')
            ->get()
            ->pluck('description', 'id');

        $types = RiskType::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $levels = RiskLevel::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return $this->render(
            $response,
            'risk/index.twig',
            [
                'risks'     => $risks,
                'systems'   => $systems,
                'processes' => $processes,
                'sources'   => $sources,
                'types'     => $types,
                'levels'    => $levels,
                'params'    => $risks->params()
            ]
        );
    }

    /**
     * Create
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     *
     * @return \Slim\Htpp\Response
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('risk.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     *
     * @return \Slim\Htpp\Response
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('risk.create');

        $attributes = RiskStoreValidator::validate($request);

        if ($swotItemId = $request->getParam('swot_item_id')) {
            $swotItem = SwotItem::findOrFail($swotItemId);

            $attributes['system_id']   = $swotItem->swot->system_id;
            $attributes['process_id']  = $swotItem->swot->process_id;
            $attributes['description'] = $swotItem->description;
        }
        
        $risk = new Risk($attributes);
        
        /** @var \Illuminate\Database\Connection $db */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $risk->save();
            
            if ($swotItemId) {
                $risk->swotItems()->syncWithoutDetaching([$swotItemId]);
            }

            $db->commit();
        } catch (Throwable $th) {
            $db->rollBack();
            throw $th;
        }

        return $this->redirect($request, $response, 'risk.view', ['risk' => $risk->id]);
    }

    /**
     * View
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $this->authorize('risk.view');

        $risk = Risk::findOrFail($args['risk']);
        
        $assessments = $risk->assessments()
            ->with('likelihood', 'consequence', 'level', 'tasks', 'tasks.completedBy', 'createdBy')
            ->orderByDesc('id')
            ->get();

        $tasks = $risk->tasks()
            ->whereNull('risk_assessment_id')
            ->with('responsible', 'completedBy')
            ->orderBy('number')
            ->get();

        return $this->render(
            $response,
            'risk/view.twig',
            [
                'risk'        => $risk,
                'assessments' => $assessments,
                'tasks'       => $tasks,
            ]
        );
    }

    /**
     * Edit
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $risk = Risk::findOrFail($args['risk']);

        $this->authorize('edit', $risk);

        return $this->createOrEdit($request, $response, $risk);
    }

    /**
     * Update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $risk = Risk::findOrFail($args['risk']);

        $this->authorize('edit', $risk);

        $attributes = RiskUpdateValidator::validate($request);

        $risk->fill($attributes);
        $risk->save();
        
        return $this->redirect($request, $response, 'risk.view', ['risk' => $risk->id]);
    }

    /**
     * Delete
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $risk = Risk::findOrFail($args['risk']);

        $this->authorize('delete', $risk);

        try {
            $risk->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'risk.view',
                [
                    'risk' => $risk->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_UI_RISK_DELETED, $risk->code));
        return $this->redirect($request, $response, 'risk.index');
    }

    /**
     * Download
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function download(Request $request, Response $response): Response
    {
        return (new Excel)->download(new RiskExporter($this->get('pdo')), $response);
    }

    /**
     * Selectize
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Htpp\Response
     */
    public function selectize(Request $request, Response $response): Response
    {
        $param = $request->getParam('q');

        $risks = Risk::where('id', $param)
            ->orWhere('description', 'like', '%' . $param . '%')
            ->limit(10)
            ->get();

        $data = [];

        foreach ($risks as $risk) {
            $data[] = [
                'value'       => $risk->id,
                'text'        => $risk->code . ' ' . $risk->description,
                'code'        => $risk->code,
                'description' => $risk->description,
            ];
        }

        return $response->withJson(compact('data'));
    }

    /**
     * Create or edit
     *
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param \App\Models\Risk|null $risk
     * 
     * @return \Slim\Http\Response
     */
    private function createOrEdit(Request $request, Response $response, Risk $risk = null): Response
    {
        $systems = System::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $processes = Process::active()
            ->internal()
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $types = RiskType::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
        
        $treatments = RiskTreatmentType::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $users = User::active()
            ->orderBy('firstname')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $sources = Source::orderBy('description')
            ->get()
            ->pluck('description', 'id')
            ->toArray();

        if ($risk) {
            $swotItem = null;
        } else {
            $swotItem = SwotItem::find($request->getParam('swot_item'));
        }

        return $this->render(
            $response,
            'risk/'.($risk? 'edit.twig': 'create.twig'),
            [
                'risk'       => $risk,
                'systems'    => $systems,
                'processes'  => $processes,
                'users'      => $users,
                'types'      => $types,
                'sources'    => $sources,
                'swot_item'  => $swotItem,
                'treatments' => $treatments,
            ]
        );
    }
}
