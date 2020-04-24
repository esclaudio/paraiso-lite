<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasAudit;

class Role extends Model
{
    use HasAudit,
        HasHistories;

    protected $table = 'roles';

    protected $fillable = [
        'name',
    ];

    // Relationships

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}
