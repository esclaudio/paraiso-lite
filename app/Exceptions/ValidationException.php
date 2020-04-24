<?php

namespace App\Exceptions;

class ValidationException extends \Exception
{
    protected $errors;

    public function __construct(array $errors)
    {
        parent::__construct('The given data was invalid.');

        $this->errors = $errors;
    }

    public function errors()
    {
        return $this->errors;
    }
}
