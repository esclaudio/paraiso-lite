<?php

namespace App\Validators;

use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RiskConsequenceValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'        => $this->request->getParam('name'),
            'description' => $this->request->getParam('description'),
            'value'       => $this->request->getParam('value'),
        ];
    }
    
    protected function getRules(): array
    {
        return [
            'name' => new Assert\Required([
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
            ]),

            'value' => new Assert\Required([
                new Assert\GreaterThan([
                    'value' => 0
                ]),
            ]),
        ];
    }
}
