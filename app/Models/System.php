<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class System extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'systems';
    
    protected $fillable = [
        'name',
    ];
}
