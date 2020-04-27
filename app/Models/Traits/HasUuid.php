<?php

namespace App\Models\Traits;

use App\Observers\UuidObserver;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::observe(UuidObserver::class);
    }
}