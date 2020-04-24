<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasComments;
use App\Models\Traits\HasAudit;
use App\Models\Traits\HasAttachments;
use App\Filters\NonconformityFilter;

class Nonconformity extends Model
{
    use HasAudit,
        HasResponsible,
        HasComments,
        HasAttachments,
        HasHistories;

    protected $table   = 'nonconformities';

    protected $fillable = [
        'system_id',
        'process_id',
        'description',
        'occurrence_date',
        'customer_id',
        'product_id',
        'quantity',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Attributes

    public function getCodeAttribute()
    {
        return $this->id;
    }
}
