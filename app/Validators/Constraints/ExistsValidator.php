<?php

namespace App\Validators\Constraints;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExistsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }
        
        $query = $constraint->query->where($constraint->field, $value);

        if ($constraint->where) {
            $where = array_replace([null, null, null], $constraint->where);
            $query->where($where[0], $where[1], $where[2]);
        }

        if (!$query->exists()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
