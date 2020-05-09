<?php

namespace App\Filters\Fields\Base;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Facades\View;

class TextField extends BaseField
{
    public function apply(Request $request, Builder $query, string $value): void
    {
        $query->where($this->name, 'like', '%'.$value.'%');
    }

    public function render(Request $request): string
    {
        return View::fetch(
            'layouts/filters/text.twig',
            [
                'name'  => $this->name,
                'label' => $this->label,
                'value' => $request->getParam($this->name),
            ]
        );
    }
}