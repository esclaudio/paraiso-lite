<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Process extends Model
{
    use HasAudit,
        HasResponsible,
        HasHistories;

    protected $table = 'processes';

    protected $fillable = [
        'name',
        'is_active',
    ];

    // Relationships
    
    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'process_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'process_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'process_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
