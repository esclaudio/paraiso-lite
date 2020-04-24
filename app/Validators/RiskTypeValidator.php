<?php

namespace App\Validators;

use App\Models\RiskType;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RiskTypeValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'        => $this->request->getParam('name'),
            'description' => $this->request->getParam('description'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('type');

        return [
            'name' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => RiskType::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
