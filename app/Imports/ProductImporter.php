<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Support\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class ProductImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Products');
    }

    public function getFields(): array
    {
        return [
            'code' => [
                'title' => trans('Code'),
                'required' => true,
            ],

            'description' => [
                'title' => trans('Description'),
                'required' => true,
            ],
        ];
    }
    
    protected function model(array $row)
    {
        if (Product::where('code', $row['code'])->exists()) {
            return null;
        }

        return new Product([
            'code'        => $row['code'],
            'description' => $row['description'],
            'created_by'  => Auth::id()
        ]);
    }
}