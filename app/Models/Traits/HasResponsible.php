<?php

namespace App\Models\Traits;

use App\Models\User;

trait HasResponsible
{
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function scopeResponsibilityOf($query, User $user)
    {
        return $query->where('responsible_id', $user->id);
    }

    public function isResponsibilityOf(User $user): bool
    {
        return $this->responsible_id == $user->id;
    }
}
