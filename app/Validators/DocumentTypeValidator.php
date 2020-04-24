<?php

namespace App\Validators;

use App\Models\DocumentType;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class DocumentTypeValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'name'         => $this->request->getParam('name'),
            'prefix'       => $this->request->getParam('prefix'),
            'next_number'  => (int)$this->request->getParam('next_number'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('document_type');

        return [
            'name' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => DocumentType::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
            
            'prefix' => new Assert\Required([
                new Assert\Length([
                    'min' => 1
                ]),
                new MyAssert\Unique([
                    'query' => DocumentType::query(),
                    'field' => 'prefix',
                    'where' => ['id', '<>', $id]
                ]),
            ]),
        ];
    }
}
