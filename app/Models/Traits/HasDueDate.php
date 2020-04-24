<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait HasDueDate
{
    // Attributes
    
    public function getOverdueAttribute(): int
    {
        //The 2nd param is to not return absoloute value and instead include the '-' sign.
        return $this->due_date->diffInDays('today', false);
    }

    public function getOverdueForHumansAttribute(): ?string
    {
        $overdue = $this->due_date->diffInDays('today', false);

        if ($overdue === 0) {
            return trans('Due today');
        } 
        
        if ($overdue === -1) {
            return trans('Due tomorrow');
        }

        if ($overdue === 1) {
            return trans('Due yesterday');
        }

        if ($overdue < 1) {
            return sprintf(trans('%s days left'), abs($overdue));
        }

        if ($overdue > 1) {
            return sprintf(trans('%s days expired'), $overdue);
        }

        return null;
    }

    // Scopes

    public function scopeOverdue( $query)
    {
        return $query
            ->whereNull('completed_at')
            ->where('due_date', '<=', date('Y-m-d'));
    }
}
