<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Product extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'products';

    protected $fillable = [
        'code',
        'description',
    ];

    // Attributes

    public function getFullDescriptionAttribute()
    {
        if ($this->code) {
            return $this->code . ' - ' . $this->description;
        }

        return $this->description;
    }
}
