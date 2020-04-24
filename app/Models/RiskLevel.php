<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class RiskLevel extends Model
{
    use HasAudit,
        HasHistories;

    protected $table   = 'risk_level';
    
    protected $fillable = [
        'name',
        'description',
        'color',
        'created_by',
        'updated_by',
    ];
}
