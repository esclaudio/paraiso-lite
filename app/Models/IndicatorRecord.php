<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class IndicatorRecord extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'indicator_record';
    
    protected $fillable = [
        'value',
        'from_date',
        'to_date',
    ];

    protected $dates = [
        'from_date',
        'to_date'
    ];

    // Relationships

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    // Attributes

    public function getDateRangeAttribute()
    {
        $diff = $this->from_date->diffInMonths($this->to_date);

        if ($diff == 0) {
            return $this->from_date->format('m/y');
        }

        if ($diff == 12) {
            return $this->from_date->format('Y');
        }

        if ($diff % 12 === 0) {
            return sprintf('%s - %s', $this->from_date->format('Y'), $this->to_date->format('Y'));
        }

        return sprintf('%s - %s', $this->from_date->format('m/y'), $this->to_date->format('m/y'));
    }
}
