<?php

namespace App\Policies;

use App\Models\{
    User,
    Nonconformity
};

class NonconformityPolicy extends Policy
{
    public function edit(User $user, Nonconformity $nonconformity): bool
    {
        return ! $nonconformity->treatment()->exists() && (
            $user->is_admin || $nonconformity->isResponsibilityOf($user)
        );
    }

    public function createTreatment(User $user, Nonconformity $nonconformity): bool
    {
        return ! $nonconformity->treatment()->exists() && (
            $user->is_admin || $nonconformity->isResponsibilityOf($user)
        );
    }

    public function deleteTreatment(User $user, Nonconformity $nonconformity): bool
    {
        return $nonconformity->treatment()->exists() && (
            $user->is_admin || $nonconformity->isResponsibilityOf($user)
        );
    }

    public function delete(User $user, Nonconformity $nonconformity): bool
    {
        return $user->is_admin ||
            (
                $nonconformity->isResponsibilityOf($user) &&
                $user->hasPermission('nonconformity.delete')
            );
    }
}
