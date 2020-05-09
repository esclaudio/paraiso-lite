<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Workflow\Contracts\StatefulContract;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;
use App\Support\Facades\Storage;

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
        'approved_at',
        'next_periodic_review_date',
    ];

    /**
     * Document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    /**
     * Is archived
     *
     */
    public function getIsArchivedAttribute(): bool
    {
        return $this->status === DocumentStatus::ARCHIVED;
    }

    /**
     * Is published
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === DocumentStatus::PUBLISHED;
    }

    /**
     * File URL
     */
    public function getFileUrlAttribute(): ?string
    {
        // return Storage::disk('s3')->temporaryUrl($this->file_path, Carbon::now()->addMinutes(15));

        if ($this->file_path) {
            return Storage::disk('s3')->url($this->file_path);
        }

        return null;
    }

    /**
     * Has file
     */
    public function getHasFileAttribute(): bool
    {
        return $this->file_path !== null;
    }

    /**
     * Get status html
     *
     * @return void
     */
    public function getStatusHtmlAttribute()
    {
        return DocumentStatus::html($this->status);
    }

    /**
     * Get the number of days since the version was published.
     *
     * @return void
     */
    public function getPublishedDaysAttribute()
    {
        if ($this->approved_at) {
            return $this->approved_at->diffInDays();
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

    // Scopes

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

    public function scopePending($query)
    {
        $query->whereIn('status', [
            DocumentStatus::DRAFT,
            DocumentStatus::REJECTED,
            DocumentStatus::TO_REVIEW,
            DocumentStatus::TO_APPROVE
        ]);
    }

    public function scopePublished($query)
    {
        $query->where('status', DocumentStatus::PUBLISHED);
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', DocumentStatus::ARCHIVED);
    }

    public function scopeNotArchived(Builder $query): void
    {
        $query->where('status', '<>', DocumentStatus::ARCHIVED);
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
     * Get state for workflow.
     */
    public function getState(): string
    {
        return $this->status;
    }

    /**
     * Set state for workflow.
     */
    public function setState(string $state): void
    {
        $this->status = $state;
    }

    /** 
     * Publish 
     */
    public function publish(): void
    {
        $document = $this->document;

        // Archive previous published version

        $document->versions()->published()->get()->each->archive();

        // Unlock document

        $document->unlock();

        // Publish new version

        $this->status = DocumentStatus::PUBLISHED;
        $this->save();
    }

    /**
     * Archive
     */
    public function archive(): void
    {
        $this->status = DocumentStatus::ARCHIVED;
        $this->save();
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
