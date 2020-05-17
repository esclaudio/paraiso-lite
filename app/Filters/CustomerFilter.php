<?php

namespace App\Filters;

use App\Support\Filter\Filter;
use App\Models\Customer;
use App\Filters\Fields\Base\TextField;

class CustomerFilter extends Filter
{
    protected $model = Customer::class;

    protected function fields(): array
    {
        return [
            new TextField('search', 'Search'),
        ];
    }
}