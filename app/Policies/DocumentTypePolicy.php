<?php

namespace App\Policies;

use App\Models\{
    User,
    DocumentType
};

class DocumentTypePolicy extends Policy
{
    public function edit(User $user, DocumentType $type): bool
    {
        return $user->is_admin || $user->hasPermission('document_type.edit');
    }

    public function view(User $user, DocumentType $type): bool
    {
        return $user->is_admin || $user->hasPermission('document_type.view');
    }

    public function delete(User $user, DocumentType $type): bool
    {
        return $user->is_admin || $user->hasPermission('document_type.delete');
    }
}
