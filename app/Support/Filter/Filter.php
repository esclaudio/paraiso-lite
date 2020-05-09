<?php

namespace App\Support\Filter;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Pagination\Paginator;

abstract class Filter
{
    /**
     * Request
     *
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * Model Name
     *
     * @var string
     */
    protected $model;

    /**
     * Query Param Name for Page
     *
     * @var string
     */
    protected $pageName = 'page';

    /**
     * Query Params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Get fields.
     *
     * @return \App\Support\Filter\Contracts\Field[]
     */
    abstract public function fields(): array;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->params = $this->clean($request->getParams());
    }

    /**
     * Get Query.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $query = (new $this->model)->newQuery();

        $this->setup($query);

        foreach($this->fields() as $field) {
            if ($value = $this->value($field->name)) {
                $field->apply($this->request, $query, $value);
            }
        }
        
        return $query;
    }

    /**
     * Paginate query.
     * 
     * @param int $perPage
     * @return \App\Support\Pagination\Paginator
     */
    public function paginate(int $perPage): Paginator
    {
        $query = $this->query();

        $currentPage = (int)$this->request->getParam($this->pageName) ?: 1;
        $total = $query->toBase()->getCountForPagination();
        $results = $total? $query->forPage($currentPage, $perPage)->get(): $query->getModel()->newCollection();
        
        return (new Paginator($results, $total, $perPage, $currentPage, $this->pageName))
            ->appends($this->params);
    }

    /**
     * Render filter.
     *
     * @return string
     */
    public function render(): string
    {
        return implode('', array_map(function ($field) {
            return $field->render($this->request);
        }, $this->fields()));
    }

    /**
     * Get value for key.
     *
     * @param string $key
     * @return string|null
     */
    public function value(string $key): ?string
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Remove empty parameteres.
     *
     * @param array $params
     * @return array
     */
    protected function clean(array $params): array
    {
        $cleaned = [];

        foreach($params as $key => $value) {
            if (mb_strlen($value) > 0) {
                $cleaned[$key] = $value;
            }
        }

        return $cleaned;
    }

    /**
     * Setup query.
     *
     * @return void
     */
    protected function setup(Builder $query): void
    {
        return;
    }
}