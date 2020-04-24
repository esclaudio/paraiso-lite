<?php

namespace App\Support\Workflow;

class Transition
{
    protected $name;
    protected $fromState;
    protected $to;
    protected $properties;

    public function __construct(string $name, State $from, State $to, array $properties = [])
    {
        $this->name       = $name;
        $this->from       = $from;
        $this->to         = $to;
        $this->properties = $properties;
    }

    public function getFrom() : State
    {
        return $this->from;
    }

    public function getTo() : State
    {
        return $this->to;
    }

    public function getName() : string
    {
        return $this->name;
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
