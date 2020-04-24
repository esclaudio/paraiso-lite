<?php

namespace App\Validators;

use App\Models\RiskLevel;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RiskLevelValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'        => $this->request->getParam('name'),
            'description' => $this->request->getParam('description'),
            'color'       => $this->request->getParam('color'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('level');

        return [
            'name' => new Assert\Required([
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => RiskLevel::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
