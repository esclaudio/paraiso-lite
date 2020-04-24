<?php

namespace App\Policies;

use App\Models\{
    User,
    RiskMatrix
};

class RiskMatrixPolicy extends Policy
{
    public function edit(User $user, RiskMatrix $matrix): bool
    {
        return $user->is_admin || $user->hasPermission('risk_matrix.edit');
    }

    public function view(User $user, RiskMatrix $matrix): bool
    {
        return $user->is_admin || $user->hasPermission('risk_matrix.view');
    }
}
