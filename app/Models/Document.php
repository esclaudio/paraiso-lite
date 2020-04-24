<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;
use App\Filters\DocumentFilter;

class Document extends Model
{
    use HasAudit,
        HasResponsible,
        HasHistories;

    protected $table = 'documents';

    protected $fillable = [
        'system_id',
        'process_id',
        'document_type_id',
        'code',
        'name',
        'reviewer_id',
        'approver_id',
        'review_frequency',
        'is_locked',
        'is_active',
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

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function involvedProcesses()
    {
        return $this->belongsToMany(Process::class, 'documents_processes', 'document_id', 'process_id');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class, 'document_id');
    }

    // Attributes

    public function getFullNameAttribute()
    {
        if ($this->code) {
            return $this->code . ' - ' . $this->name;
        }

        return $this->name;
    }

    public function getReviewFrequencyDescriptionAttribute()
    {
        return FrequencyType::description(FrequencyType::MONTH, $this->review_frequency);
    }

    public function getLatestVersionAttribute()
    {
        return $this->versions()
            ->latest()
            ->first();
    }

    public function getPublishedVersionAttribute()
    {
        return $this->versions()
            ->published()
            ->latest()
            ->first();
    }

    public function getNextVersionAttribute()
    {
        $lastVersion = $this->versions()
            ->latest()
            ->first();
        
        $version = $lastVersion->version ?? 0;

        return ++$version;
    }

    public function getInvolvedProcessesNamesAttribute(): string
    {
        if ($this->involvedProcesses->count() === 0) {
            return trans('None');
        }

        return $this->involvedProcesses->pluck('name')->implode(', ');
    }


    // Functions

    public function lock(): void
    {
        $this->is_locked = true;
        $this->save();
    }

    public function unlock(): void
    {
        $this->is_locked = false;
        $this->save();
    }

    // Boot
    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $documentType = $model->documentType;

            if ($documentType->prefix) {
                $model->code = $documentType->next_code;
                $documentType->next_number++;
                $documentType->save();
            }
        });
    }
}
