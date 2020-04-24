<?php

namespace App\Models\Traits;

use App\Observers\AuditObserver;
use App\Models\User;

trait HasAudit
{
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function bootHasAudit(): void
    {
        static::observe(AuditObserver::class);
    }
}
