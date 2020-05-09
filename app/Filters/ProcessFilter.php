<?php

namespace App\Filters;

use App\Support\Filter\Filter;
use App\Support\Filter\Field\TextFieldFilter;
use App\Models\Process;

class ProcessFilter extends Filter
{
    protected $model = Process::class;

    protected function fields(): array
    {
        return [
            new TextFieldFilter('name', 'Name'),
        ];
    }
}