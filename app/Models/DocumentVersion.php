<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Support\Workflow\Contracts\StatefulContract;
use App\Support\Facades\Storage;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class DocumentVersion extends Model implements StatefulContract
{
    use HasAudit,
        HasHistories;

    protected $table = 'documents_versions';

    protected $fillable = [
        'version',
        'changes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'reviewd_at',
        'approved_at',
    ];

    /**
     * Document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    /**
     * Reviewed by
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Approved by
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the URL of the file.
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::disk('s3')->url($this->file_path);
        }

        return null;
    }

    /**
     * Return if the model has a file.
     */
    public function getHasFileAttribute(): bool
    {
        return $this->file_path !== null;
    }

    /**
     * Get the HTML of the model status.
     */
    public function getStatusHtmlAttribute(): string
    {
        return DocumentStatus::html($this->status);
    }

    /**
     * Get the number of days since the version was published.
     */
    public function getPublishedDaysAttribute(): ?int
    {
        if ($this->approved_at) {
            return (int)$this->approved_at->diffInDays();
        }

        return null;
    }

    public function getResponsibleAttribute()
    {
        if ($this->status == DocumentStatus::TO_REVIEW) {
            return $this->document->reviewer;
        }

        if ($this->status == DocumentStatus::TO_APPROVE) {
            return $this->document->approver;
        }

        return $this->document->responsible;
    }

    public function scopePendingOf($query, User $user)
    {
        $query->whereHas('document', function ($query) use ($user) {
            $query->where(function ($query) use ($user) {
                $query->where([
                    ['document_version.status', DocumentStatus::TO_REVIEW],
                    ['document.reviewer_id', $user->id]
                ]);
            })->orWhere(function ($query) use ($user) {
                $query->whereIn('document_version.status', [DocumentStatus::DRAFT, DocumentStatus::REJECTED])
                ->where('document.responsible_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where([
                    ['document_version.status', DocumentStatus::TO_APPROVE],
                    ['document.approver_id', $user->id]
                ]);
            });
        });
    }

    /**
     * Scope for published.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', DocumentStatus::PUBLISHED);
    }


    /**
     * Upload file.
     */
    public function uploadFile(UploadedFile $upload): void
    {
        $extension = pathinfo($upload->getClientFilename(), PATHINFO_EXTENSION);
        $path = sprintf('documents/%s.%s', Uuid::uuid4(), $extension);

        if (Storage::disk('s3')->put($path, $upload, 'public')) {
            $this->file_path = $path;
            $this->save();
        }
    }

    /**
     * Get the state for workflow.
     */
    public function getState(): string
    {
        return $this->status;
    }

    /**
     * Set the state for workflow.
     */
    public function setState(string $state): void
    {
        $this->status = $state;
    }

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->document->lock();
        });
    }
}
