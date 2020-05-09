<?php

namespace App\Filters\Fields;

use Slim\Http\Request;
use App\Filters\Fields\Base\SelectField;
use App\Models\Process;

class ProcessField extends SelectField
{
    public function __construct()
    {
        parent::__construct('process_id', trans('Process'));
    }

    protected function options(Request $request): array
    {
        return Process::orderBy('name')
            ->active()
            ->pluck('name', 'id')
            ->toArray();
    } 
}