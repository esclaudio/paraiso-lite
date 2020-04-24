<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class CustomerImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Customers');
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
        return new Customer([
            'code'       => $row['code'],
            'name'       => $row['name'],
            'created_by' => Auth::id()
        ]);
    }
}