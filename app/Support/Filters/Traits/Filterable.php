<?php

namespace App\Support\Filters\Traits;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Pagination\Paginator;

trait Filterable
{
    /**
     * Request used to filter
     * 
     * @var Slim\Http\Request
     */
    protected $filterRequest;

    /**
     * Cleaned filter parameters
     * 
     * @var array
     */
    protected $filterParams = [];

    public function scopeFilter(Builder $query, Request $request, string $filterClass = null)
    {
        $this->filterRequest = $request;
        
        if ($filterClass === null) {
            $filterClass = $this->defaultFilter;
        }

        if ($filterClass) {
            $filter = new $filterClass($query, $request->getParams());
            $filter->apply();
            $this->filterParams = $filter->params();
        }

        return $query;
    }

    public function scopePaginateFilter(Builder $query, int $perPage, string $pageName = 'page')
    {
        $currentPage = (int)$this->filterRequest->getParam($pageName) ?: 1;
        $total = $query->toBase()->getCountForPagination();
        $results = $total? $query->forPage($currentPage, $perPage)->get(): $query->getModel()->newCollection();
        
        return (new Paginator($results, $total, $perPage, $currentPage, $pageName))
            ->appends($this->filterParams);
    }
}