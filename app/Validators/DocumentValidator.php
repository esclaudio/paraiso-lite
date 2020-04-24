<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validators\Constraints as MyAssert;
use App\Models\User;
use App\Models\System;
use App\Models\Process;
use App\Models\DocumentType;
use App\Models\Document;


class DocumentValidator extends Validator
{
    protected function getInput(): array
    {
        $id = $this->request->getAttribute('document');

        $input = [
            'code'             => $this->request->getParam('code'),
            'name'             => $this->request->getParam('name'),
            'system_id'        => $this->request->getParam('system_id'),
            'process_id'       => $this->request->getParam('process_id'),
            'responsible_id'   => $this->request->getParam('responsible_id'),
            'reviewer_id'      => $this->request->getParam('reviewer_id'),
            'approver_id'      => $this->request->getParam('approver_id'),
            'observations'     => $this->request->getParam('observations'),
            'review_frequency' => $this->request->getParam('review_frequency'),
        ];

        // Store

        if ( ! $id) {
            $input['document_type_id'] = $this->request->getParam('document_type_id');
        }

        return $input;
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('document');

        $rules = [
            'name' => new Assert\Required([
                new MyAssert\Unique([
                    'query' => Document::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
            ]),
            'system_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => System::query(),
                    'field' => 'id'
                ]),
            ]),
            'process_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => Process::query(),
                    'field' => 'id'
                ]),
            ]),
            'responsible_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => User::query(),
                    'field' => 'id'
                ]),
            ]),
            'reviewer_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => User::query(),
                    'field' => 'id'
                ]),
            ]),
            'approver_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => User::query(),
                    'field' => 'id'
                ]),
            ]),
        ];

        // Store
        
        if ( ! $id) {
            $rules['code'] = new Assert\Optional([
                new MyAssert\Unique([
                    'query' => Document::query(),
                    'field' => 'code',
                ]),
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
            ]);

            $rules['document_type_id'] = new Assert\Required([
                new MyAssert\Exists([
                    'query' => DocumentType::query(),
                    'field' => 'id'
                ]),
            ]);
        }

        return $rules;
    }
}
