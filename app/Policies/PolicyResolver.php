<?php

namespace App\Policies;

class PolicyResolver
{
    protected $namespace = 'App\Policies';

    /**
     * Get a policy instance for the given class.
     *
     * @param mixed $model
     * @return Policy
     */
    public static function for($model): Policy
    {
        $className = (new \ReflectionClass($model))->getShortName();
        $policy = sprintf('%s\%sPolicy', $this->namespace, $className);

        if ( ! is_object($model)) {
            $model = null;
        }

        return new $policy($model);
    }
}
