<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;

class EquipmentFilter extends Filter
{
    protected function applyCode(string $value)
    {
        $this->query->where('code', 'like', "{$value}%");
    }

    protected function applyDescription(string $value)
    {
        $this->query->where('description', 'like', "%{$value}%");
    }

    protected function applyEquipmentTypeId(string $value)
    {
        $this->query->where('equipment_type_id', (int)$value);
    }

    protected function applyEquipmentLocationId(string $value)
    {
        $this->query->where('equipment_location_id', (int)$value);
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }

    protected function applyStatus(string $value)
    {
        if ($value == 'inactive') {
            $this->query->where('is_active', false);
        } else if ($value == 'active') {
            $this->query->where('is_active', true);
        }
    }

    protected function applyWithUnplannedTasks(string $value)
    {
        if ($value == '1') {
            $this->query->has('unplannedTasks');
        }
    }
}