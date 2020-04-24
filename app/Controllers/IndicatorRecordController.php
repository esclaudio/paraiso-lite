<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Carbon\Carbon;
use App\Models\IndicatorRecord;
use App\Models\Indicator;
use App\Support\Datatable\Datatable;

class IndicatorRecordController extends Controller
{
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
        $indicator = Indicator::findOrFail($args['indicator']);

        $this->authorize('create_record', $indicator);

        $value = $request->getParam('value');

        if ($value !== null) {
            $value = (float)str_replace(',', '.', $value);
        }

        /**
         * @var \Illuminate\Database\Connection $db
         */
        $db = $this->get('db');
        $db->beginTransaction();
        
        try {
            $record = $indicator->createRecord($value);

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        return $this->redirect($request, $response, 'indicator.index');
    }

    /**
     * Update
     *
     * @param  \Slim\Http\Request $request
     * @param  \Slim\Http\Response $response
     * @param  array $args
     *
     * @return \Slim\Http\Response
     * @throws \App\Exceptions\AuthorizationException
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $record = IndicatorRecord::with([
                'indicator',
                'indicator.objectives'
            ])
            ->where([
                ['id', $args['record']],
                ['indicator_id', $args['indicator']],
            ])
            ->firstOrFail();

        $this->authorize('edit', $record->indicator);
        
        $originalValue = is_null($record->value)? null: (float)$record->value;
        $newValue = $request->getParam('value');
        
        if ($newValue !== null) {
            $newValue = (float)str_replace(',', '.', $newValue);
        }
        
        /**
         * @var \Illuminate\Database\Connection $db
         */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $record->value = $newValue;
            $record->observations = $request->getParam('observations');
            $record->save();

            if ($newValue !== $originalValue) {
                $record->indicator->objectives->each->calculate();
            }

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        return $this->redirect(
            $request,
            $response,
            'indicator.view',
            [
                'indicator' => $record->indicator_id
            ]
        );
    }

    /**
     * Datatable
     *
     * @param  \Slim\Http\Request $request
     * @param  \Slim\Http\Response $response
     * @param  array $args
     *
     * @return \Slim\Http\Response
     * @throws \App\Exceptions\AuthorizationException
     */
    public function datatable(Request $request, Response $response, array $args): Response
    {
        $this->authorize('indicator.view');

        $indicator = Indicator::findOrFail($args['indicator']);

        $router = $this->get('router');
        $canEdit = $this->user->can('edit', $indicator);

        $records = IndicatorRecord::select([
                'indicator_record.id',
                'indicator_record.indicator_id',
                'indicator_record.from_date',
                'indicator_record.to_date',
                'indicator_record.value',
                'indicator_record.observations',
            ])
            ->where('indicator_record.indicator_id', $indicator->id)
            ->getQuery();

        return (new Datatable($records, $request, $response))
            ->addColumn('period', function ($row) {
                if ($row->from_date == $row->to_date) {
                    return (new Carbon($row->from_date))->format('m/Y');
                }

                return sprintf(
                    '%s - %s', 
                    (new Carbon($row->from_date))->format('m/Y'),
                    (new Carbon($row->to_date))->format('m/Y')
                );
            })
            ->addColumn('formatted_value', function ($row) use ($indicator) {
                return sprintf('%s %s', 
                    number_format($row->value, $indicator->decimals, ',', '.'),
                    $indicator->unit
                );
            })
            ->addColumn('update_url', function ($row) use ($router, $canEdit) {
                if ($canEdit) {
                    return $router->pathFor(
                        'indicator_record.update',
                        [
                            'indicator' => $row->indicator_id,
                            'record' => $row->id
                        ]
                    );
                }

                return null;
            })
            ->response();
    }
}
