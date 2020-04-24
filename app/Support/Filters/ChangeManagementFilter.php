<?php

namespace App\Filters;

use DateTime;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ChangeManagementFilter extends Filter
{
    protected function applyCode(string $value)
    {
        // Remove any letter from string
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->query->where('id', (int)$value);
    }

    protected function applyTitle(string $value)
    {
        $this->query->where('title', 'like', "%{$value}%");
    }

    protected function applyStatus(string $value)
    {
        $this->query->where('status', $value);
    }

    protected function applySystemId(string $value)
    {
        $this->query->where('system_id', (int)$value);
    }

    protected function applyProcessId(string $value)
    {
        $this->query->where('process_id', (int)$value);
    }

    protected function applySourceId(string $value)
    {
        $this->query->where('source_id', (int)$value);
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }

    protected function applyApproverId(string $value)
    {
        $this->query->where('approver_id', (int)$value);
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

    protected function applyCondition(string $value)
    {
        switch ($value) {
            case 'without_tasks':
                $this->query->doesntHave('tasks');
                break;

            case 'with_overdue_tasks':
                $this->related('tasks', function (Builder $query) {
                    $query->overdue();
                });
                break;
        }
    }
}