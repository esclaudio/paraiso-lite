<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validators\Constraints as MyAssert;
use App\Models\DocumentVersion;

class DocumentVersionValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'version'  => $this->request->getParam('version'),
            'changes'  => $this->request->getParam('changes'),
        ];
    }

    protected function getRules(): array
    {
        $versionId = $this->request->getAttribute('version');
        $documentId = $this->request->getAttribute('document');

        return [
            'changes' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 2
                ]),
            ]),

            'version' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Unique([
                    'query' => DocumentVersion::query(),
                    'field' => 'version',
                    'where' => [
                        ['id', '<>', $versionId],
                        ['document_id', '=', $documentId],
                    ],
                ]),
            ]),
        ];
    }
}
