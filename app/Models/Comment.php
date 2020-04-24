<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Comment extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'comment';
    protected $fillable = ['message'];

    // Relationships

    public function commentable()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // Scopes

    public function scopeByModel($query, $model)
    {
        return $query->where('model_type', get_class($model))->where('model_id', $model->id);
    }
}
