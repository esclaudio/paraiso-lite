<?php

namespace App\Filters;

use DateTime;

class TrainingFilter extends Filter
{
    protected function applyCode(string $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        $this->query->where('id', (int)$value);
    }

    protected function applyCourseId(string $value)
    {
        $this->query->where('course_id', (int)$value);
    }

    protected function applyStatus(string $value)
    {
        $this->query->where('status', $value);
    }

    protected function applyPlannedDateFrom(string $value)
    {
        $date = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('planned_date', '>=', $date->format('Y-m-d'));
    }

    protected function applyPlannedDateTo(string $value)
    {
        $date = DateTime::createFromFormat(DATE_FORMAT, $value);
        $this->query->whereDate('planned_date', '<=', $date->format('Y-m-d'));
    }
}