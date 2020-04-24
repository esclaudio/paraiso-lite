<?php

namespace App\Support\Filters;

use ReflectionClass;
use Psr\Http\Message\RequestInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Query
     * 
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Related queries
     * 
     * @var array
     */
    protected $related = [];

    /**
     * Constructor
     * 
     * @param \Illuminate\Database\Eloquent\Builder $builder Builder
     * @param array                                 $params  Parameters
     * 
     * @return void
     */
    public function __construct(Builder $query, array $params)
    {
        $this->query = $query;
        $this->params = $this->clean($params);
    }

    /**
     * Get parameters
     * 
     * @param array $only
     * 
     * @return array
     */
    public function params(array $only = []): array
    {
        if (count($only)) {
            $onlyParams = [];

            foreach($only as $key) {
                if (array_key_exists($key, $this->params)) {
                    $onlyParams[$key] = $this->params[$key];
                }
            }

            return $onlyParams;
        }

        return $this->params;
    }

    /**
     * Remove empty parameteres
     * 
     * @return array
     */
    public function clean(array $params): array
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
     * Get parameter value
     * 
     * @param string $value
     * @param mixed  $default
     * 
     * @return string|null
     */
    public function value(string $key, $default = null)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return $default;
    }

    /**
     * Apply filters
     * 
     * @return void
     */
    public function apply()
    {
        $this->setup();
        
        // Model filters

        foreach($this->params as $filter => $value) {
            $handler = 'apply' . $this->camelize($filter);

            if (method_exists($this,  $handler)) {
                $this->{$handler}($value);
            }
        }

        // Related filters

        foreach($this->related as $relation => $callbacks) {
            $this->query->whereHas($relation, function ($query) use ($callbacks) {
                foreach($callbacks as $callback) {
                    $callback($query);
                }
            });
        }
    }

    /**
     * Add related filter
     * 
     * @param string   $relation
     * @param callable $callback
     * 
     * @return void
     */
    protected function related(string $relation, callable $callback)
    {
        $this->related[$relation][] = $callback;
    }

    /**
     * Setup
     * 
     * @return void
     */
    protected function setup()
    {
        //
    }

    /**
     * Camelize a string
     * 
     * @param string $input     Input
     * @param string $separator Separator
     * 
     * @return string
     */
    protected function camelize(string $input, string $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    protected function fullTextWildcards($term)
    {
        $words = explode(' ', $term);
 
        foreach($words as $key => $word) {
            if(strlen($word) >= 3) {
                $words[$key] = '+' . $word . '*';
            }
        }
 
        $searchTerm = implode(' ', $words);
 
        return $searchTerm;
    }
}