<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RiskAssessment;

class RiskAssessmentPolicy extends Policy
{
    public function edit(User $user, RiskAssessment $assessment): bool
    {
        return $user->is_admin || $assessment->risk->isResponsibilityOf($user);
    }    
    public function delete(User $user, RiskAssessment $assessment): bool
    {
        return $user->is_admin || $assessment->risk->isResponsibilityOf($user);
    }
}
