<?php

namespace App\Filters;

use DateTime;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class RiskFilter extends Filter
{
    protected function applyCode(string $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->query->where('id', (int)$value);
    }

    protected function applyDescription(string $value)
    {
        $this->query->where('description', 'like', "%{$value}%");
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

    protected function applyRiskTypeId(string $value)
    {
        $this->query->where('risk_type_id', (int)$value);
    }

    protected function applyAnalysisLevelId(string $value)
    {
        $this->query->where('risk_level_id', (int)$value);
    }

    protected function applyLastAssessmentLevelId(string $value)
    {
        $this->related('lastAssessment', function (Builder $subquery) use ($value) {
            $subquery->where('risk_level_id', (int)$value);
        });
    }

    protected function applyCondition(string $value)
    {
        switch ($value) {
            case 'with_unassessed_tasks':
                $this->query->has('tasks');
                break;

            case 'without_tasks':
                $this->query->doesntHave('allTasks');
                break;

            case 'with_overdue_tasks':
                $this->query->whereHas('tasks', function (Builder $subquery) {
                    $subquery->overdue();
                });
                break;
        }
    }

    protected function applyCreatedFromDate(string $value)
    {
        $date = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('created_at', '>=', $date->format('Y-m-d'));
    }

    protected function applyCreatedToDate(string $value)
    {
        $date = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('created_at', '<=', $date->format('Y-m-d'));
    }
}