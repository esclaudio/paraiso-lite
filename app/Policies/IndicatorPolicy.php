<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Indicator;

class IndicatorPolicy extends Policy
{
    public function edit(User $user, Indicator $indicator): bool
    {
        return $user->is_admin || $indicator->isResponsibilityOf($user);
    }

    public function delete(User $user, Indicator $indicator): bool
    {
        return $user->is_admin ||
            (
                $indicator->isResponsibilityOf($user) &&
                $user->hasPermission('indicator.delete')
            )
        ;
    }

    public function createRecord(User $user, Indicator $indicator): bool
    {
        return $indicator->is_active &&
            $indicator->next_record_is_available &&
            (
                 $user->is_admin ||
                 $indicator->isResponsibilityOf($user)
            )
        ;
    }

    public function activate(User $user, Indicator $indicator)
    {
        return ! $indicator->is_active && (
            $user->is_admin || $indicator->isResponsibilityOf($user)
        );
    }

    public function deactivate(User $user, Indicator $indicator)
    {
        return $indicator->is_active && (
            $user->is_admin || $indicator->isResponsibilityOf($user)
        );
    }
}
