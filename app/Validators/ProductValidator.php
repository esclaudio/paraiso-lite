<?php

namespace App\Validators;

use App\Models\Product;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ProductValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'code'        => $this->request->getParam('code'),
            'description' => $this->request->getParam('description'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('product');

        return [
            'code' => new Assert\Required([
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Product::query(),
                    'field' => 'code',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
            
            'description' => new Assert\Required([
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Product::query(),
                    'field' => 'description',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
