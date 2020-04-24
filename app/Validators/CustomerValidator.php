<?php

namespace App\Validators;

use App\Models\Customer;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'code' => $this->request->getParam('code'),
            'name' => $this->request->getParam('name'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('customer');

        return [
            'code' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Customer::query(),
                    'field' => 'code',
                    'where' => ['id', '<>', $id]
                ]),
            ]),

            'name' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Customer::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
