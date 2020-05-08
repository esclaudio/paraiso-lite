<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DocumentVersion;
use App\Models\DocumentStatus;

class DocumentVersionPolicy extends Policy
{
    public function edit(User $user, DocumentVersion $version): bool
    {
        return in_array($version->status, [DocumentStatus::DRAFT, DocumentStatus::REJECTED])
            && ($user->is_admin || $version->document->isResponsibilityOf($user));
    }

    public function destroy(User $user, DocumentVersion $version): bool
    {
        return in_array($version->status, [DocumentStatus::DRAFT, DocumentStatus::REJECTED])
            && ($user->is_admin || $version->document->isResponsibilityOf($user));
    }
}
