<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Models\DocumentStatus;

class CurrentDocumentFilter extends DocumentFilter
{
    protected function setup(Builder $query): void
    {
        $query->whereHas('versions', function (Builder $subquery) {
            $subquery->where('status', DocumentStatus::PUBLISHED);
        });
    }
}