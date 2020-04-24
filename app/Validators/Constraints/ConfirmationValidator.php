<?php

namespace App\Validators\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConfirmationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $key = $constraint->key;
        $root = $this->context->getRoot();

        if (isset($root[$key]) && $root[$key] != $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ path }}', $key)
                ->addViolation();
        }
    }
}
