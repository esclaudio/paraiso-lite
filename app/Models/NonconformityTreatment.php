<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class NonconformityTreatment extends Model
{
    use HasAudit,
        HasHistories;

    protected $table   = 'nonconformity_treatment';
    protected $guarded = ['id'];

    // Relationships

    public function nonconformity()
    {
        return $this->belongsTo(Nonconformity::class, 'nonconformity_id');
    }

    public function type()
    {
        return $this->belongsTo(NonconformityTreatmentType::class, 'nonconformity_treatment_type_id');
    }
}
