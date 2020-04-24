<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasComments;
use App\Models\Traits\HasAudit;
use App\Models\Traits\HasAttachments;
use App\Filters\RiskFilter;

class Risk extends Model
{
    use HasAudit,
        HasResponsible,
        HasComments,
        HasAttachments,
        HasHistories;

    protected $table   = 'risk';

    protected $fillable = [
        'system_id',
        'process_id',
        'source_id',
        'risk_type_id',
        'description',
        'impact',
        'responsible_id',
        'risk_likelihood_id',
        'risk_consequence_id',
        'risk_level_id',
        'risk_treatment_type_id',
        'swot_item_id',
        'observations',
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

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function type()
    {
        return $this->belongsTo(RiskType::class, 'risk_type_id');
    }

    public function swotItems()
    {
        return $this->belongsToMany(SwotItem::class, 'risk_swot_item', 'risk_id', 'swot_item_id');
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

    public function treatment()
    {
        return $this->belongsTo(RiskTreatmentType::class, 'risk_treatment_type_id');
    }

    public function assessments()
    {
        return $this->hasMany(RiskAssessment::class, 'risk_id');
    }

    public function lastAssessment()
    {
        return $this->hasOne(RiskAssessment::class, 'risk_id')->latest();
    }

    public function tasks()
    {
        return $this->hasMany(RiskTask::class, 'risk_id')->whereNull('risk_assessment_id');
    }

    public function allTasks()
    {
        return $this->hasMany(RiskTask::class, 'risk_id');
    }

    // Attributes

    public function getCodeAttribute()
    {
        return sprintf("RO-%04d", $this->id);
    }

    // Functions

    

    // Boot
    
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $matrix = RiskMatrix::where([
                ['risk_type_id', $model->risk_type_id],
                ['risk_likelihood_id', $model->risk_likelihood_id],
                ['risk_consequence_id', $model->risk_consequence_id],
            ])->first();
    
            if ($matrix) {
                $model->risk_level_id = $matrix->risk_level_id;
            }
        });
    }
}
