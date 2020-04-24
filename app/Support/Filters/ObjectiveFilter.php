<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ObjectiveFilter extends Filter
{
    protected function setup()    {
        if ($this->value('status') === null) {
            $this->active();
        }
    }

    protected function applyCode(string $value)
    {
        // Remove any letter from string
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->query->where('id', (int)$value);
    }

    protected function applyDescription(string $value)
    {
        $this->query->where('description', 'like', "%{$value}%");
    }

    protected function applyStatus(string $value)
    {
        if ($value === 'inactive') {
            $this->inactive();
        } elseif ($value === 'active') {
            $this->active();
        }
    }

    protected function applyPolicyId(string $value)
    {
        $this->related('policies', function ($query) use ($value) {
            $query->where('id', $value);
        });
    }

    protected function applyIndicatorId(string $value)
    {
        $this->query->where('indicator_id', (int)$value);
    }

    protected function applySystemId(string $value)
    {
        $this->related('indicator', function ($query) use ($value) {
            $query->where('system_id', (int)$value);
        });
    }
    
    protected function applyProcessId(string $value)
    {
        $this->related('indicator', function ($query) use ($value) {
            $query->where('process_id', (int)$value);
        });
    }

    protected function active()
    {
        $this->query->where('is_active', true);
    }

    protected function inactive()
    {
        $this->query->where('is_active', false);
    }
}