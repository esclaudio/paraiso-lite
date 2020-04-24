<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Workflow\Contracts\StatefulContract;
use App\Unoconv\Unoconv;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;
use App\Models\DocumentTransition;

class DocumentVersion extends Model implements StatefulContract
{
    use HasAudit,
        HasHistories;

    const DOCUMENTS_PATH = 'documents';

    protected $table = 'document_version';

    protected $fillable = [
        'version',
        'changes',
        'file',
        'preview',
        'extension',
        'mimetype',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
        'next_periodic_review_date',
    ];

    // Relationships

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function transitions()
    {
        return $this->hasMany(DocumentVersionTransition::class, 'document_version_id');
    }

    public function lastTransition()
    {
        return $this->transitions()->latest();
    }

    // Attributes

    public function getIsArchivedAttribute(): bool
    {
        return $this->status === DocumentStatus::ARCHIVED;
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === DocumentStatus::PUBLISHED;
    }

    public function getFilePathAttribute()
    {
        if ( ! $this->file) {
            return null;
        }

        return storage_path(sprintf('%s/%s', self::DOCUMENTS_PATH, $this->file));
    }

    public function getFileExistsAttribute()
    {
        return file_exists($this->file_path);
    }

    public function getPreviewPathAttribute()
    {
        if ( ! $this->preview) {
            return null;
            
        }

        return storage_path(sprintf('%s/%s', self::DOCUMENTS_PATH, $this->preview));
    }

    public function getPreviewExistsAttribute()
    {
        return file_exists($this->preview_path);
    }

    public function getStatusDescriptionAttribute()
    {
        return DocumentStatus::description($this->status);
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


    // Functions

    public function uploadFile(UploadedFile $upload)
    {
        if ( ! storage_make(self::DOCUMENTS_PATH)) {
            throw new \Exception('Can not create ' . self::DOCUMENTS_PATH . ' folder');
        }

        $this->unlink();
        
        $this->file = Uuid::uuid4()->toString();
        $this->extension = pathinfo($upload->getClientFilename())['extension'] ?? null;
        $this->mimetype = $upload->getClientMediaType();

        $upload->moveTo($this->file_path);
    }

    public function makePreview(Unoconv $unoconv)
    {
        $filePath = $this->file_path;

        if ($unoconv->canConvertTo('pdf', $filePath)) {
            $this->preview = $this->file . '__preview.pdf';
            $unoconv->convertToPdf($filePath, $this->preview_path);
        } else {
            if ($this->mimetype === 'application/pdf') {
                $type = 'pdf';
            } else {
                list($type, $subtype) = explode('/', $this->mimetype);
            }

            if (in_array($type, ['pdf', 'image', 'video', 'audio'])) {
                $this->preview = $this->file;
            }
        }
    }

    public function uploadPreview(UploadedFile $upload)
    {
        if ( ! storage_make(self::DOCUMENTS_PATH)) {
            throw new \Exception('Can not create ' . self::DOCUMENTS_PATH . ' folder');
        }

        if ($upload->getClientMediaType() !== 'application/pdf') {
            throw new \Exception('Preview file must be a PDF file');
        }

        $this->unlinkPreview();
        $this->preview = $this->file . '__preview.pdf';

        $upload->moveTo($this->preview_path);
    }

    public function unlinkFile()
    {
        $path = $this->file_path;

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function unlinkPreview()
    {
        $path = $this->preview_path;

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function unlink()
    {
        $this->unlinkFile();
        $this->unlinkPreview();
    }

    public function getState() : string
    {
        return $this->status;
    }

    public function setState(string $state)
    {
        $this->status = $state;
    }

    public function publish()
    {
        $document = $this->document;

        // Archive previous published version

        $document->versions()->published()->get()->each->archive();

        // Unlock document

        $document->unlock();

        // Publish new version

        $this->status = DocumentStatus::PUBLISHED;
        $this->published_at = Carbon::now();
        $this->next_periodic_review_date = Carbon::today()->addMonths($document->review_frequency);
        $this->save();
    }

    public function archive()
    {
        $this->status = DocumentStatus::ARCHIVED;
        $this->next_periodic_review_date = null;
        $this->save();
    }

    public function reviewed(User $user)
    {
        if ($this->status === DocumentStatus::PUBLISHED) {
            $this->next_periodic_review_date = Carbon::today()->addMonths($this->document->review_frequency);
            $this->save();

            $this->transitions()->create([
                'transition' => DocumentTransition::REVIEWED,
                'created_by' => $user->id,
            ]);
        }
    }

    // Boot

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->status = DocumentStatus::DRAFT;
            $model->document->lock();
        });
    }
}
