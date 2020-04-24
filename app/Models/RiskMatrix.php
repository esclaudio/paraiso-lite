<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasHistories;

class RiskMatrix extends Model
{
    use HasHistories;
    
    protected $table = 'risk_matrix';

    protected $fillable = [
        'risk_type_id',
        'risk_likelihood_id',
        'risk_consequence_id',
        'risk_level_id',
    ];

    public $incrementing = false;
    
    public $timestamps = false;

    // Relationships

    public function type()
    {
        return $this->belongsTo(RiskType::class, 'risk_type_id');
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

    // Scopes

    public function scopeOfType(Builder $query, RiskType $type)
    {
        $query->where('risk_type_id', $type->id);
    }

    // Attributes

    public function getCodeAttribute()
    {
        return sprintf('%s-%s', $this->risk_likelihood_id, $this->risk_consequence_id);
    }
}
