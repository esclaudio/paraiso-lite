<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validators\Constraints as MyAssert;
use App\Models\Process;

class ProcessValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'      => $this->request->getParam('name'),
            'is_active' => (bool)$this->request->getParam('is_active', true),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('process');

        return [
            'name' => new Assert\Required([
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Process::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
