<?php

namespace App\Support\Workflow;

class State
{
    const INITIAL_STATE = 'I';
    const NORMAL_STATE  = 'N';
    const FINAL_STATE   = 'F';

    public $name;
    public $type;
    public $properties;

    public function __construct(string $name, string $type, array $properties = [])
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->properties = $properties;
    }

    public function isInitial() : bool
    {
        return $this->type == INITIAL_STATE;
    }

    public function isNormal() : bool
    {
        return $this->type == NORMAL_STATE;
    }

    public function isFinal() : bool
    {
        return $this->type == FINAL_STATE;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function getProperty($key)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }

        return null;
    }

    public function __toString()
    {
        return $name;
    }
}
