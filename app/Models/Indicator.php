<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;
use App\Filters\IndicatorFilter;

class Indicator extends Model
{
    use HasAudit,
        HasResponsible,
        HasHistories;

    protected $table = 'indicators';
    
    protected $fillable = [
        'system_id',
        'process_id',
        'name',
        'description',
        'frequency',
        'decimals',
        'unit',
        'responsible_id',
        'start_date',
    ];

    protected $dates = [
        'next_record_date',
        'start_date',
    ];

    // Relationships

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function records()
    {
        return $this->hasMany(IndicatorRecord::class, 'indicator_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Attributes

    public function getFrequencyDescriptionAttribute()
    {
        return FrequencyType::description(FrequencyType::MONTH, $this->frequency);
    }

    public function getNextRecordDateRangeAttribute()
    {
        $toDate = $this->next_record_date->copy();
        
        if ($this->frequency == 1) {
            return $toDate->format('m/y');
        }
        
        $fromDate = $toDate->copy()->subMonths($this->frequency - 1);

        return sprintf('%s - %s', $fromDate->format('m/y'), $toDate->format('m/y'));
    }

    public function getNextRecordIsAvailableAttribute()
    {
        return $this->next_record_available_date->isPast();
    }

    // Functions

    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function calculateNextRecordDate(): void
    {
        if ( ! $this->next_record_date) {
            $this->next_record_date = $this->start_date;
        } else {
            $this->next_record_date = $this->next_record_date->copy()->addMonths($this->frequency);
        }
    }

    public function createRecord(float $value): IndicatorRecord
    {
        if ( ! $this->next_record_is_available) {
            throw new \Exception(sprintf('Next record is available from %s', $this->next_record_date->format('m/Y')));
        }

        // Calculate record date range

        $fromDate = $this->next_record_date->copy()->subMonths($this->frequency - 1);
        $toDate = $this->next_record_date->copy();

        // Create the new record

        $record = new IndicatorRecord;
        $record->from_date = $fromDate;
        $record->to_date = $toDate;

        if ($value === null) {
            $record->value = null;    
        } else {
            $record->value = (float)$value;
        }
        
        $this->records()->save($record);

        // Calculate next record date

        $nextRecordDate =  $this->next_record_date->copy()->addMonths($this->frequency);

        // Update the indicator next record date

        $this->next_record_date = $nextRecordDate;
        $this->save();

        // Objective

        $objectives = $this->objectives()
            ->active()
            ->get();

        foreach ($objectives as $objective) {
            $objective->calculate();
        }

        return $record;
    }

    public function getChartData(Carbon $startDate = null, Carbon $endDate = null): array
    {
        if ( ! $startDate) {
            $startDate = Carbon::today()->subYears(2);
        }

        if ( ! $endDate) {
            $endDate = Carbon::today();
        }

        $records = $this->records()
            ->where('from_date', '>=', $startDate->format('Y-m-d'))
            ->where('to_date', '<=', $endDate->format('Y-m-d'))
            ->orderBy('from_date')
            ->get();

        $labels = [];
        $data = [];
        
        foreach ($records as $record) {
            $labels[] = $record->date_range;
            $data[] = $record->value;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label'                => trans('Indicator'),
                    'fill'                 => false,
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 10,
                    'lineTension'          => 0,
                    'borderColor'          => '#3498db',
                    'data'                 => $data,
                    'pointBackgroundColor' => '#3498db',
                    'backgroundColor'      => '#3498db',
                    'snapGaps'             => true,
                    'type'                 => 'line',
                ],
            ]
        ];
    }

    // Boot

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->calculateNextRecordDate();
        });
    }
}
