<?php

namespace App\Validators;

use App\Models\System;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class SystemValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'   => $this->request->getParam('name'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('system');

        return [
            'name' => new Assert\Required([
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => System::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
