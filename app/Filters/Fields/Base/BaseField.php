<?php

namespace App\Filters\Fields\Base;

use App\Support\Filter\Contracts\Field;

abstract class BaseField implements Field
{
    public $name;
    public $label;
    
    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }
}