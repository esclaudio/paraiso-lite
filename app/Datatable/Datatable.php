<?php

namespace App\Datatable;

use Slim\Http\Response;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Pagination\Paginator;
use App\Models\Customer;

abstract class Datatable
{
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function response(): Response
    {
        $query = $this->query();

        $this->filter($query);

        $total = $query->count();

        $this->order($query);

        $items = $this->paginate($query)
            ->map(function ($item) {
                return $this->transform($item);
            });

        return $this->response->withJson([
            'items' => $items,
            'total' => $total,
        ]);
    }

    protected abstract function query(): Builder;
    protected abstract function order(Builder $query): void;
    protected abstract function filter(Builder $query): void;
    protected abstract function transform(Model $model): array;

    protected function paginate(Builder $query): Collection
    {
        $perPage = (int)$this->request->getParam('per_page') ?: 10;
        $currentPage = (int)$this->request->getParam('page') ?: 1;

        $total = $query->toBase()->getCountForPagination();
        
        return $total
            ? $query->forPage($currentPage, $perPage)->get()
            : $query->getModel()->newCollection();
    }
}