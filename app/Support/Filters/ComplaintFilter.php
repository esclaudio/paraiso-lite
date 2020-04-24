<?php

namespace App\Filters;

use DateTime;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ComplaintFilter extends Filter
{
    protected function applyId(string $value) {
        $this->query->where('id', (int)$value);
    }

    protected function applyTitle(string $value) {
        $this->query->where('title', 'like', "%{$value}%");
    }

    protected function applyProduct(string $value)
    {
        $this->related('product', function (Builder $query) use ($value) {
            $query->where('description', 'like', '%' . $value . '%')
                ->orWhere('code', 'like', $value . '%');
        });
    }

    protected function applyCustomer(string $value)
    {
        $this->related('customer', function (Builder $query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%')
                ->orWhere('code', 'like', $value . '%');
        });
    }

    protected function applyComplaintCategoryId(string $value) {
        $this->query->where('complaint_category_id', (int)$value);
    }

    protected function applySystemId(string $value)
    {
        $this->query->where('system_id', (int)$value);
    }

    protected function applyProcessId(string $value)
    {
        $this->query->where('process_id', (int)$value);
    }

    protected function applyStatus(string $value)
    {
        if ($value == 'closed') {
            $this->query->whereNotNull('closed_at');
        } else if ($value == 'open') {
            $this->query->whereNull('closed_at');
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

    protected function applyIsCompanyResponsibility(string $value)
    {
        if ($value === '1') {
            $this->query->where('is_company_responsibility', true);
        }
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }
}