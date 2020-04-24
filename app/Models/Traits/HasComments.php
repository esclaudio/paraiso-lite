<?php

namespace App\Models\Traits;

use App\Models\Comment;

trait HasComments
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'model', 'model_type', 'model_id');
    }
}
