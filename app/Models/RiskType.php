<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class RiskType extends Model
{
    use HasAudit,
        HasHistories;

    protected $table   = 'risks_types';

    protected $fillable = [
        'name',
    ];
}
