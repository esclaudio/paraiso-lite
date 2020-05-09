<?php

namespace App\Filters;

use App\Support\Filter\Filter;
use App\Models\DocumentType;
use App\Models\Document;
use App\Filters\Fields\SystemField;
use App\Filters\Fields\ProcessField;
use App\Filters\Fields\DocumentOnlyNewField;
use App\Filters\Fields\CodeOrNameField;
use App\Filters\Fields\Base\SelectField;

class DocumentFilter extends Filter
{
    protected $model = Document::class;

    public function fields(): array
    {
        return [
            new CodeOrNameField,
            new SelectField('document_type_id', trans('Type'), function () {
                return DocumentType::orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();
            }),
            new SystemField,
            new ProcessField,
            new DocumentOnlyNewField,
        ];
    }
}