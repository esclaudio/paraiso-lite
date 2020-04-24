<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use DateTime;
use App\Models\MaintenanceInternalStatus;

class MaintenanceFilter extends Filter
{
    protected function setup()
    {
        if ($this->value('status') === null) {
            $this->query->where('status', MaintenanceInternalStatus::OPEN);
        }
    }

    protected function applyStatus(string $value)
    {
        if ($value !== 'all') {
            $this->query->where('status', $value);
        }
    }

    protected function applyNumber(string $value)
    {
        $this->query->where('id', (int)$value);
    }

    protected function applyType(string $value)
    {
        $this->query->where('type', $value);
    }

    protected function applyEquipmentCode(string $value)
    {
        $this->related('equipment', function (Builder $query) use ($value) {
            $query->where('code', 'like', "$value%");
        });
    }

    protected function applyEquipmentDescription(string $value)
    {
        $this->related('equipment', function (Builder $query) use ($value) {
            $query->where('description', 'like', "%$value%");
        });
    }

    protected function applyPriority(string $value)
    {
        $this->query->where('priority', $value);
    }

    protected function applyCategory(string $value)
    {
        $this->query->where('maintenance_category_id', $value);
    }

    protected function applyEquipmentTypeId(string $value)
    {
        $this->related('equipment', function (Builder $query) use ($value) {
            $query->where('equipment_type_id', (int)$value);
        });
    }

    protected function applyEquipmentLocationId(string $value)
    {
        $this->related('equipment', function (Builder $query) use ($value) {
            $query->where('equipment_location_id', (int)$value);
        });
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }

    protected function applyMaintenanceStatusId(string $value)
    {
        $this->query->where('maintenance_status_id', (int)$value);
    }

    protected function applyFromDueDate(string $value)
    {
        $fromDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('due_date', '>=', $fromDate->format('Y-m-d'));
    }

    protected function applyToDueDate(string $value)
    {
        $toDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('due_date', '<=', $toDate->format('Y-m-d'));
    }

    protected function applyCompletedFromDate(string $value)
    {
        $fromDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('completed_at', '>=', $fromDate->format('Y-m-d'));
    }

    protected function applyCompletedToDate(string $value)
    {
        $toDate = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('completed_at', '<=', $toDate->format('Y-m-d'));
    }

    protected function applyObservations(string $value)
    {
        $this->query->where(function (Builder $query) use ($value) {
            $query->where('observations', 'like', '%'. $value . '%')
                ->orWhere('final_observations', 'like', '%' . $value . '%');
        });
    }
}