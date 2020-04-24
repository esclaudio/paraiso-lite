<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class RiskLikelihood extends Model
{
    use HasAudit,
        HasHistories;

    protected $table   = 'risk_likelihood';
    
    protected $fillable = [
        'name',
        'description',
        'value',
        'created_by',
        'updated_by',
    ];

    // Relationships

    public function type()
    {
        return $this->belongsTo(RiskType::class, 'risk_type_id');
    }

    // Scopes

    public function scopeOfType(Builder $query, RiskType $type)
    {
        $query->where('risk_type_id', $type->id);
    }
}
