<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Source;
use App\Support\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class SourceImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Sources');
    }

    public function getFields(): array
    {
        return [
            'description' => [
                'title' => trans('Description'),
                'required' => true,
            ],
        ];
    }
    
    protected function model(array $row)
    {
        return new Source([
            'description' => $row['description'],
            'created_by'  => Auth::id()
        ]);
    }
}