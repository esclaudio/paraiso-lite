<?php

namespace App\Policies;

use App\Models\{
    User,
    RiskConsequence
};

class RiskConsequencePolicy extends Policy
{
    public function edit(User $user, RiskConsequence $consequence): bool
    {
        return $user->is_admin || $user->hasPermission('risk_consequence.edit');
    }

    public function delete(User $user, RiskConsequence $consequence): bool
    {
        return $user->is_admin || $user->hasPermission('risk_consequence.delete');
    }
}
