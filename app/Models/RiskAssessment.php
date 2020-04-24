<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class RiskAssessment extends Model
{
    use HasAudit,
        HasHistories;

    protected $table   = 'risk_assessment';

    protected $fillable = [
        'risk_likelihood_id',
        'risk_consequence_id',
        'conclusions',
    ];

    // Relationships

    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function likelihood()
    {
        return $this->belongsTo(RiskLikelihood::class, 'risk_likelihood_id');
    }

    public function consequence()
    {
        return $this->belongsTo(RiskConsequence::class, 'risk_consequence_id');
    }

    public function level()
    {
        return $this->belongsTo(RiskLevel::class, 'risk_level_id');
    }

    public function tasks()
    {
        return $this->hasMany(RiskTask::class, 'risk_assessment_id');
    }

    // Boot
    
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $matrix = RiskMatrix::where([
                ['risk_type_id', $model->risk->risk_type_id],
                ['risk_likelihood_id', $model->risk_likelihood_id],
                ['risk_consequence_id', $model->risk_consequence_id],
            ])->first();
    
            if ($matrix) {
                $model->risk_level_id = $matrix->risk_level_id;
            }
        });
    }
}
