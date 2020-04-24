<?php

namespace App\Validators\Constraints;

use Symfony\Component\Validator\Constraint;

class Exists extends Constraint
{
    public $message = 'The value "{{ value }}" does not exists.';

    /**
     * Model
     * @var Illuminate\Database\Eloquent\Builder
     */
    public $query;

    /**
     * Field
     * @var string
     */
    public $field;

    /**
     * Where
     * @var mixed
     */
    public $where;
}
