<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;

class EvidenceFilter extends Filter
{
    protected function applyTitle(string $value)
    {
        $this->query->where('title', 'like', "%{$value}%");
    }

    protected function applySystemId(string $value)
    {
        $this->query->where('system_id', (int)$value);
    }

    protected function applyProcessId(string $value)
    {
        $this->query->where('process_id', (int)$value);
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }
}