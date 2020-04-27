<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

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
    
    /**
     * System
     */
    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    /**
     * Process
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    /**
     * Document type
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    /**
     * Reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Involved procesess
     */
    public function involvedProcesses(): BelongsToMany
    {
        return $this->belongsToMany(Process::class, 'documents_processes', 'document_id', 'process_id');
    }

    /**
     * Versions
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id');
    }

    /**
     * Scope locked
     */
    public function scopeLocked(Builder $query): void
    {
        $query->where('is_locked', true);
    }

    /**
     * Scope unlocked
     */
    public function scopeUnlocked(Builder $query): void
    {
        $query->where('is_locked', false);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
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

    /**
     * Get last version
     *
     * @return void
     */
    public function getLatestVersion(): ?DocumentVersion
    {
        return $this->versions()
            ->latest()
            ->first();
    }

    /**
     * Get current version
     */
    public function getCurrentVersion(): ?DocumentVersion
    {
        return $this->versions()
            ->published()
            ->latest()
            ->first();
    }

    public function getNextVersion(): int
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
