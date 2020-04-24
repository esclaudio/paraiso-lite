<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Attachment extends Model
{
    use HasAudit,
        HasHistories;

    const ATTACHMENTS_PATH = 'attachments';
    const PREVIEWS_PATH = 'public/previews';
    const PREVIEWS_URL = 'storage/previews';
    const ICONS = [
        'image' => 'fa-file-image-o',
        'audio' => 'fa-file-audio-o',
        'video' => 'fa-file-video-o',
        
        'application/pdf' => 'fa-file-pdf-o',
        
        'application/msword' => 'fa-file-word-o',
        'application/vnd.ms-word' => 'fa-file-word-o',
        'application/vnd.oasis.opendocument.text' => 'fa-file-word-o',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o',
        
        'application/vnd.ms-excel' => 'fa-file-excel-o',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel-o',
        'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel-o',
    
        'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint-o',
        'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint-o',
        
        'text/plain' => 'fa-file-text-o',
        'text/html' => 'fa-file-code-o',
        
        'application/json' => 'fa-file-code-o',
    ];

    protected $table = 'attachment';

    protected $fillable = [
        'id',
        'name',
        'extension',
        'type',
        'size'
    ];

    public $incrementing = false;

    // Relationships

    public function attachable()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // Attributes

    public function getNameWithExtensionAttribute(): string
    {
        return "{$this->name}.{$this->extension}";
    }

    public function getIconAttribute(): string
    {
        $type = explode('/', $this->type)[0];

        return self::ICONS[$type] ?? self::ICONS[$this->type] ?? 'fa-file-o';
    }

    public function getPathAttribute(): string
    {
        return storage_path(sprintf('%s/%s', self::ATTACHMENTS_PATH, $this->id));
    }

    // Scopes

    public function scopeOfModel(Builder $query, $model)
    {
        $query->where([
            ['model_type', get_class($model)],
            ['model_id', $model->id],
        ]);
    }

    // Functions

    public function fillFromUploadedFile(UploadedFile $file)
    {
        $info = pathinfo($file->getClientFilename());

        if (!$this->id) {
            $this->id = Uuid::uuid4()->toString();
        }

        $this->name      = isset($info['filename'])? $info['filename']: null;
        $this->extension = isset($info['extension'])? $info['extension']: null;
        $this->type      = $file->getClientMediaType();
        $this->size      = $file->getSize();
    }

    public function upload(UploadedFile $file)
    {
        $this->fillFromUploadedFile($file);

        if (file_exists($this->path)) {
            unlink($this->path);
        }

        if (storage_make(self::ATTACHMENTS_PATH)) {
            $file->moveTo($this->path);
        }
    }

    public function unlink()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    private function getIcons(): array
    {
        return [
            'image' => 'fa-file-image-o',
            'audio' => 'fa-file-audio-o',
            'video' => 'fa-file-video-o',
            
            'application/pdf' => 'fa-file-pdf-o',
            
            'application/msword' => 'fa-file-word-o',
            'application/vnd.ms-word' => 'fa-file-word-o',
            'application/vnd.oasis.opendocument.text' => 'fa-file-word-o',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o',
            
            'application/vnd.ms-excel' => 'fa-file-excel-o',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel-o',
            'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel-o',
        
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint-o',
            'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint-o',
            
            'text/plain' => 'fa-file-text-o',
            'text/html' => 'fa-file-code-o',
            
            'application/json' => 'fa-file-code-o',
        ];
    }

    private function getSimpleType(): string
    {
        if (substr($this->type, 0, 5) === 'image') {
            return 'image';
        }

        if (substr($this->type, 0, 5) === 'audio') {
            return 'audio';
        }

        if (substr($this->type, 0, 5) === 'video') {
            return 'video';
        }
    
        return $this->type;
    }
}
