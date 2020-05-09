<?php

namespace App\Filters\Fields;

use Slim\Http\Request;
use App\Filters\Fields\Base\SelectField;
use App\Models\System;

class SystemField extends SelectField
{
    public function __construct()
    {
        parent::__construct('system_id', trans('System'));
    }

    protected function options(Request $request): array
    {
        return System::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}