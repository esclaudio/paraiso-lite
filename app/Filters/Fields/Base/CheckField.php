<?php

namespace App\Filters\Fields\Base;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Facades\View;

class CheckField extends BaseField
{
    public function apply(Request $request, Builder $query, string $value): void
    {
        if ('1' === $value) {
            $query->where($this->name, true);
        }
    }

    public function render(Request $request): string
    {
        return View::fetch(
            'layouts/filters/check.twig',
            [
                'name'    => $this->name,
                'label'   => $this->label,
                'checked' => '1' === $request->getParam($this->name),
            ]
        );
    }
}