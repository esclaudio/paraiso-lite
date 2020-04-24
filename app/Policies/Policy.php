<?php

namespace App\Policies;

use App\Models\User;

abstract class Policy
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the entity has a given ability
     * @param  User   $user    User
     * @param  string $ability Ability
     * @param  mixed $model   Model
     * @return bool
     */
    public function can(User $user, string $ability): bool
    {
        $ability = $this->normalize($ability);

        if (method_exists($this, $ability)) {
            return $this->$ability($user, $this->model);
        }

        throw new \Exception("Ability $ability not found");
    }

    /**
     * Convert snake_case to camelCase
     * @param  string $value Value
     * @return string
     */
    protected function normalize(string $value): string
    {
        return lcfirst(str_replace('_', '', ucwords($value, '_')));
    }
}
