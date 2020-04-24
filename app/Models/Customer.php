<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Customer extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'customers';

    protected $fillable = [
        'code',
        'name',
    ];

    // Attributes
    
    public function getFullNameAttribute()
    {
        if ($this->code) {
            return $this->code . ' - ' . $this->name;
        }

        return $this->name;
    }
}
