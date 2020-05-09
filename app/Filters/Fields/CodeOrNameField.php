<?php

namespace App\Filters\Fields;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\Fields\Base\TextField;

class CodeOrNameField extends TextField
{
    public function __construct()
    {
        parent::__construct('code_or_name', trans('Code or Name'));
    }

    public function apply(Request $request, Builder $query, string $value): void
    {
        $query->where(function (Builder $query) use ($value) {
            $query->where('code', 'like', '%'.$value.'%')
                ->orWhere('name', 'like', '%'.$value.'%');
        });
    }
}