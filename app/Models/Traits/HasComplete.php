<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use App\Models\User;

trait HasComplete
{
    // Relationships

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Attributes

    public function getIsCompleteAttribute()
    {
        return $this->completed_at !== null;
    }

    public function getIsIncompleteAttribute()
    {
        return $this->completed_at === null;
    }

    // Scopes

    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    // Functions

    public function complete(User $user)
    {
        $this->completed_at = Carbon::now();
        $this->completed_by = $user->id;
        $this->save();
    }

    public function reopen()
    {
        $this->completed_at = null;
        $this->completed_by = null;
        $this->save();
    }
}
