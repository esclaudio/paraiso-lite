<?php

namespace App\Models\Traits;

use App\Models\Attachment;

trait HasAttachments
{
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'model', 'model_type', 'model_id');
    }
}
