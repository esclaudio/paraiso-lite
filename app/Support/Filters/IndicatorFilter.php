<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;

class IndicatorFilter extends Filter
{
    protected function setup()
    {
        if ($this->value('status') === null) {
            $this->query->where('is_active', true);
        }
    }

    protected function applyCode(string $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->query->where('id', (int)$value);
    }

    protected function applyName(string $value)
    {
        $this->query->where('name', 'like', "%{$value}%");
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

    protected function applyStatus(string $value)
    {
        if ($value === 'inactive') {
            $this->query->where('is_active', false);
        } else {
            $this->query->where('is_active', true);
        }
    }
}