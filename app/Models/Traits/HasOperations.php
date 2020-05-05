<?php

namespace App\Models\Traits;

use App\Models\History;
use App\Support\Facades\Request;

trait HasOperations
{
    /**
     * Get all of the agent's operations.
     */
    public function operations()
    {
        return $this->hasMany(History::class, 'user_id');
    }

    public function addOperation(string $event, array $metadata = []): void
    {
        $this->operations()->create([
            'event'      => $event,
            'metadata'   => $metadata,
            'ip_address' => Request::getServerParam('REMOTE_ADDR'),
        ]);
    }
}