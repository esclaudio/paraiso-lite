<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class PersonFilter extends Filter
{
    protected function applyEmployeeNumber(string $value)
    {
        $this->query->where('employee_number', $value);
    }

    protected function applyName(string $value)
    {
        $this->query->where(function (Builder $query) use ($value) {
            $query->where('firstname', 'like', "%{$value}%")
                ->orWhere('lastname', 'like', "%{$value}%");
        });
    }

    protected function applyJobPositionId(string $value)
    {
        $this->related('jobPositions', function (Builder $query) use ($value) {
            $query->where('id', (int)$value);
        });
    }

    protected function applyLineManagerId(string $value)
    {
        $this->query->where('line_manager_id', (int)$value);
    }
}