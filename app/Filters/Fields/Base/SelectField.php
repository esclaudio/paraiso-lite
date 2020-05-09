<?php

namespace App\Filters\Fields\Base;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Facades\View;

class SelectField extends BaseField
{
    protected $options;

    public function __construct(string $name, string $label, $options = [])
    {
        $this->options = $options;

        parent::__construct($name, $label);
    }

    public function apply(Request $request, Builder $query, string $value): void
    {
        $query->where($this->name, $value);
    }

    public function render(Request $request): string
    {
        return View::fetch(
            'layouts/filters/select.twig',
            [
                'name'    => $this->name,
                'label'   => $this->label,
                'options' => $this->options($request),
                'value'   => $request->getParam($this->name),
            ]
        );
    }

    protected function options(Request $request): array
    {
        if (is_callable($this->options)) {
            return ($this->options)($request);
        }
        
        return $this->options;
    }
}