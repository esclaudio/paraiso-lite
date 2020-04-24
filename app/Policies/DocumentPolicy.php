<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;

class DocumentPolicy extends Policy
{
    public function edit(User $user, Document $document): bool
    {
        return ! $document->is_locked && (
            $user->is_admin ||
            $user->id == $document->responsible_id
        );
    }

    public function revert(User $user, Document $document): bool
    {
        return ! $document->is_locked && $user->is_admin;
    }

    public function delete(User $user, Document $document): bool
    {
        return ! $document->is_locked && (
            $user->is_admin ||
            (
                $user->id == $document->responsible_id &&
                $user->hasPermission('document.delete')
            )
        );
    }

    public function download(User $user, Document $document): bool
    {
        return $user->is_admin || $user->hasPermission('document.download');
    }

    public function print(User $user, Document $document): bool
    {
        return $user->is_admin || $user->hasPermission('document.print');
    }
}
