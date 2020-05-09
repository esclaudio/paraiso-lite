<?php

namespace App\Support\Filter\Contracts;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

interface Field
{
    public function render(Request $request): string;
    public function apply(Request $request, Builder $query, string $value): void;
}