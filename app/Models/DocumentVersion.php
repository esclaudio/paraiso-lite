<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Support\Workflow\Contracts\StatefulContract;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;
use App\Models\DocumentTransition;
use App\Facades\Storage;

class DocumentVersion extends Model implements StatefulContract
{
    use HasAudit,
        HasHistories,
        HasUuid;

    const DOCUMENTS_PATH = 'documents';

    protected $table = 'documents_versions';

    protected $fillable = [
        'version',
        'changes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
        'next_periodic_review_date',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

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
     * File path
     */
    public function getFilePathAttribute(): string
    {
        return sprintf('%s/%s', self::DOCUMENTS_PATH, $this->id);
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Has file
     */
    public function getHasFileAttribute(): bool
    {
        return Storage::exists($this->file_path);
    }

    /**
     * Preview file path
     */
    public function getPreviewPathAttribute(): string
    {
        return sprintf('%s/%s__preview.pdf', self::DOCUMENTS_PATH, $this->id);
    }

    /**
     * Has preview file
     */
    public function getHasPreviewAttribute(): bool
    {
        return Storage::exists($this->preview_path);
    }

    public function getPreviewUrlAttribute(): string
    {
        return Storage::url($this->preview_path);
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



    

    public function getStatusColorAttribute()
    {
        return DocumentStatus::color($this->status);
    }

    public function getOnInitialStateAttribute()
    {
        return in_array($this->status, [DocumentStatus::DRAFT, DocumentStatus::REJECTED]);
    }

    public function getPublishedDaysAttribute()
    {
        if ($this->published_at) {
            return $this->published_at->diffInDays();
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
     * Upload file
     */
    public function uploadFile(UploadedFile $upload)
    {
        // if ( ! storage_make(self::DOCUMENTS_PATH)) {
        //     throw new \Exception('Can not create ' . self::DOCUMENTS_PATH . ' folder');
        // }

        $this->id = Uuid::uuid4()->toString();
        $this->file_extension = pathinfo($upload->getClientFilename())['extension'] ?? null;
        $this->file_mimetype = $upload->getClientMediaType();
        $this->file_size = $upload->getSize();

        // $upload->moveTo($this->file_path);
        $stream = fopen($upload->file, 'r+');
        Storage::writeStream($this->file_path, $stream);
        dd($this->has_file, $this->file_url);
    }

    /**
     * Upload preview file
     */
    public function uploadPreview(UploadedFile $upload)
    {
        // if ( ! storage_make(self::DOCUMENTS_PATH)) {
        //     throw new \Exception('Can not create ' . self::DOCUMENTS_PATH . ' folder');
        // }

        if ($upload->getClientMediaType() !== 'application/pdf') {
            throw new \Exception('Preview file must be a PDF file');
        }

        // $upload->moveTo($this->preview_path);
        $stream = fopen($upload->file, 'r+');
        Storage::writeStream($this->preview_path, $stream);
    }

    /**
     * Get state
     */
    public function getState(): string
    {
        return $this->status;
    }

    /**
     * Set state
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
