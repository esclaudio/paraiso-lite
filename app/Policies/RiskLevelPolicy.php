<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RiskLevel;

class RiskLevelPolicy extends Policy
{
    public function edit(User $user, RiskLevel $likelihood): bool
    {
        return $user->is_admin || $user->hasPermission('risk_level.edit');
    }

    public function delete(User $user, RiskLevel $likelihood): bool
    {
        return $user->is_admin || $user->hasPermission('risk_level.delete');
    }
}
