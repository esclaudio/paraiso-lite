<?php

namespace App\Filters;

use DateTime;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class NonconformityFilter extends Filter
{
     protected function applyId(string $value)
    {
        $this->query->where('id', (int)$value);
    }

    protected function applyName(string $value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    protected function applyProduct(string $value)
    {
        $this->related('product', function (Builder $query) use ($value) {
            $query->where('description', 'like', '%' . $value . '%')
                ->orWhere('code', 'like', $value . '%');
        });
    }

    protected function applySystemId(string $value)
    {
        $this->query->where('system_id', (int)$value);
    }

    protected function applyProcessId(string $value)
    {
        $this->query->where('process_id', (int)$value);
    }

    protected function applyNonconformityTypeId(string $value)
    {
        $this->query->where('nonconformity_type_id', (int)$value);
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }

    protected function applyNonconformityTreatmentTypeId(string $value)
    {
        $value = (int)$value;

        if ($value > 0) {
            $this->related('treatment', function ($query) use ($value) {
                $query->where('nonconformity_treatment_type_id', $value);
            });
        } else {
            $this->query->whereDoesntHave('treatment');
        }
    }

    protected function applyFromDate(string $value)
    {
        $fromDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('created_at', '>=', $fromDate->format('Y-m-d'));
    }

    protected function applyToDate(string $value)
    {
        $toDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('created_at', '<=', $toDate->format('Y-m-d'));
    }
}