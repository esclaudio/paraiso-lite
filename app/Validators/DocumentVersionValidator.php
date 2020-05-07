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
            'file'     => get_upload('file', $this->request),
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

            'file' => new Assert\Required([
                new MyAssert\Upload([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        'text/plain',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.oasis.opendocument.text',
                        'application/vnd.oasis.opendocument.spreadsheet',
                    ],
                ]),
            ]),
        ];
    }
}
