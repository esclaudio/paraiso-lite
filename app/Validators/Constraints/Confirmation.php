<?php

namespace App\Validators\Constraints;

use Symfony\Component\Validator\Constraint;

class Confirmation extends Constraint
{
    public $message = 'This value must be equal to {{ path }}.';

    /**
     * Key
     * @var string
     */
    public $key;
}
