<?php

namespace App\Support\Workflow;

class State
{
    public $name;
    public $type;
    public $properties;

    public function __construct(string $name, array $properties = [])
    {
        $this->name       = $name;
        $this->properties = $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProperties(): array
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
        return $this->name;
    }
}
