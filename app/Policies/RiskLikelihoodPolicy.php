<?php

namespace App\Policies;

use App\Models\{
    User,
    RiskLikelihood
};

class RiskLikelihoodPolicy extends Policy
{
    public function edit(User $user, RiskLikelihood $likelihood): bool
    {
        return $user->is_admin || $user->hasPermission('risk_likelihood.edit');
    }

    public function delete(User $user, RiskLikelihood $likelihood): bool
    {
        return $user->is_admin || $user->hasPermission('risk_likelihood.delete');
    }
}
