<?php

namespace App\Models\Traits;

use App\Observers\HistoryObserver;
use App\Models\History;

trait HasHistories
{
    /**
     * Get all of the model's histories.
     */
    public function histories()
    {
        return $this->morphMany(History::class, 'model');
    }

    /**
     * Get all of the model's histories.
     *
     * @return void
     */
    public static function bootHasHistories()
    {
        static::observe(HistoryObserver::class);
    }
}