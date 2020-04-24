<?php

namespace App\Validators;

use App\Models\Role;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RoleValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name' => $this->request->getParam('name'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('role');

        return [
            'name' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Role::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
