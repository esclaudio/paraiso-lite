<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class DocumentType extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'documents_types';

    protected $fillable = [
        'name',
        'prefix',
        'next_number',
    ];

    // Attributes
    
    public function getNextCodeAttribute(): string
    {
        return $this->prefix? sprintf('%s-%04d', $this->prefix, $this->next_number): '';
    }
}
