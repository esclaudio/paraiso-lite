<?php

namespace App\Policies;

use App\Models\{
    User,
    RiskAnalysis
};

class RiskAnalysisPolicy extends Policy
{
    public function edit(User $user, RiskAnalysis $analysis): bool
    {
        return $user->can('edit', $analysis->risk);
    }

    public function delete(User $user, RiskAnalysis $analysis): bool
    {
        return $user->is_admin || $user->id == $analysis->risk->responsible_id;
    }
}
