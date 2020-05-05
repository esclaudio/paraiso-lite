<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Provider;
use App\Support\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class ProviderImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Providers');
    }

    public function getFields(): array
    {
        return [
            'code' => [
                'title' => trans('Code'),
                'required' => true,
            ],

            'name' => [
                'title' => trans('Name'),
                'required' => true,
            ],
        ];
    }
    
    protected function model(array $row)
    {
        return new Provider([
            'code'       => $row['code'],
            'name'       => $row['name'],
            'created_by' => Auth::id()
        ]);
    }
}