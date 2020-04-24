<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DocumentVersion;

class DocumentVersionPolicy extends Policy
{
    public function edit(User $user, DocumentVersion $version): bool
    {
        return $version->on_initial_state && (
            $user->is_admin ||
            $version->document->isResponsibilityOf($user)
        );
    }

    public function delete(User $user, DocumentVersion $version): bool
    {
        return $version->on_initial_state && (
            $user->is_admin ||
            $version->document->isResponsibilityOf($user)
        );
    }
}
