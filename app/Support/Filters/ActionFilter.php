<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;
use Carbon\Carbon;

class ActionFilter extends Filter
{
    protected function setup()    {
        if ($this->value('status') === null) {
            $this->open();
        }
    }

    protected function applyNumber(string $value)
    {
        $this->query->where('number', (int)$value);
    }

    protected function applyTitle(string $value)
    {

        $this->query->whereRaw('MATCH(title) AGAINST (? IN BOOLEAN MODE)', $this->fullTextWildcards($value));
    }

    protected function applyActionTypeId(string $value)
    {
        $this->query->where('action_type_id', (int)$value);
    }

    protected function applyStatus(string $value)
    {
        if ($value === 'open') {
            $this->open();
        } elseif ($value === 'closed') {
            $this->closed();
        }
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

    protected function applySourceId(string $value)
    {
        $this->query->where('source_id', (int)$value);
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
            case 'without_analysis':
                $this->query->doesntHave('analysis');
                break;

            case 'without_tasks':
                $this->query->has('analysis')->doesntHave('tasks');
                break;

            case 'with_overdue_tasks':
                $this->related('tasks', function (Builder $query) {
                    $query->whereNull('completed_at')
                        ->whereDate('expiration_date', '<=', Carbon::today());
                });
                break;

            case 'without_verification':
                $this->query->doesntHave('verification')
                    ->whereHas('tasks')
                    ->whereDoesntHave('tasks', function (Builder $query) {
                        $query->whereNull('completed_at');
                    });
                break;
            
            case 'with_verification':
                $this->query->has('verification');
                break;

            case 'with_overdue_verification':
                $this->query->whereHas('verification', function (Builder $query) {
                    $query->whereDate('expiration_date', '<=', Carbon::today());
                });
                break;
        }
    }

    protected function open()
    {
        $this->query->doesntHave('result');
    }

    protected function closed()
    {
        $effective = $this->value('is_effective');

        $this->query->whereHas('result', function ($query) use ($effective) {
            if ($effective !== null) {
                $query->where('is_effective', $effective === '1');
            }
        });
    }
}