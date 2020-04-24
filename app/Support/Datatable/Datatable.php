<?php

namespace App\Support\Datatable;

use Slim\Http\Response;
use Slim\Http\Request;
use Illuminate\Database\Query\Builder;

class Datatable
{
    /**
     * Query
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * Response
     *
     * @var \Slim\Http\Response
     */
    protected $response;

    /**
     * Params
     *
     * @var array
     */
    protected $params;

    /**
     * Extra columns
     *
     * @var array
     */
    protected $extraColumns;

    public function __construct(Builder $query, Request $request, Response $response)
    {
        $this->query = $query;
        $this->response = $response;
        $this->params = $request->getQueryParams();
    }

    public function response(): Response
    {
        return $this->response->withJson([
			"draw"            => (int)$this->params['draw'],
			"data"            => $this->data(),
			"recordsTotal"    => $this->count(),
			"recordsFiltered" => $this->filteredCount(),
        ]);
    }

    public function addColumn(string $name, callable $formatter)
    {
        $this->extraColumns[] = [
            'name' => $name,
            'formatter' => $formatter,
        ];

        return $this;
    }

    protected function query(): Builder
    {
        return clone $this->query;
    }

    protected function count(): int
    {
        return $this->query()->count();
    }

    protected function filteredCount(): int
    {
        $query = $this->query();

        $this->filter($query);

        return $query->count();
    }

    protected function data(): array
    {
        $query = $this->query();

        $this->filter($query);
        $this->order($query);
        $this->limit($query);

        $data = $query->get();

        foreach($data as $row) {
            foreach((array)$this->extraColumns as $extraColumn) {
                $row->{$extraColumn['name']} = $extraColumn['formatter']($row);
            }
        }

        return $data->toArray();
    }

    protected function filter(Builder $query): Builder
    {
        $columns = $this->params['columns'];

        if (isset($this->params['search']) && $this->params['search']['value']) {
			$keyword = $this->params['search']['value'];
            
            $query->where(function ($query) use ($columns, $keyword) {
                foreach($columns as $column) {
                    if ($column['searchable'] === 'true') {
                        $field = $column['name'] ?? $column['data'];

                        $query->orWhere($field, 'LIKE', "%{$keyword}%");
                    }
                }
            });
		}

		// TODO: Individual column filtering
		// for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
		// 	$requestColumn = $request['columns'][$i];
		// 	$columnIdx = array_search( $requestColumn['data'], $dtColumns );
		// 	$column = $columns[ $columnIdx ];
        //
		// 	$str = $requestColumn['search']['value'];
        //
		// 	if ( $requestColumn['searchable'] == 'true' &&
		// 	 $str != '' ) {
		// 		$binding = self::bind( $bindings, '%'.$str.'%', \PDO::PARAM_STR );
		// 		$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
		// 	}
        // }
        
        return $query;
    }

    protected function order(Builder $query): void
    {
        $columns = $this->params['columns'];

        if (isset($this->params['order'])) {
            foreach($this->params['order'] as $order) {
                $column = $columns[$order['column']];
                $field = $column['name'] ?? $column['data'];

                if ($column['orderable'] === 'true') {
                    $query->orderBy($field, $order['dir']);
                }
            }
        }
    }

    protected function limit(Builder $query): void
    {
        $query->limit((int)$this->params['length']);
        $query->offset((int)$this->params['start']);
    }
}
