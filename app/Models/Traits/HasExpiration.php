<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait HasExpiration
{
    public function getDaysToExpireAttribute()
    {
        //The 2nd param is to not return absoloute value and instead include the '-' sign.
        return Carbon::now()->diffInDays($this->expiration_date, false);
    }

    public function getIsExpiredAttribute()
    {
        return !$this->expiration_date->isFuture();
    }
}
