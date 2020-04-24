<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class MaintenanceRequestFilter extends Filter
{
    protected function applyStatus(string $value)
    {
        $this->query->where('status', $value);
    }

    protected function applyNumber(string $value)
    {
        $this->query->where('id', (int)$value);
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

    protected function applyIsUrgent(string $value)
    {
        $this->query->where('is_urgent', (bool)$value);
    }
}