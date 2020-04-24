<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Risk;

class RiskPolicy extends Policy
{
    public function edit(User $user, Risk $risk): bool
    {
        return $user->is_admin || $risk->isResponsibilityOf($user);
    }

    public function delete(User $user, Risk $risk): bool
    {
        return $user->is_admin || (
            $risk->isResponsibilityOf($user) && $user->hasPermission('risk.delete')
        );
    }

    public function createAssessments(User $user, Risk $risk): bool
    {
        return
            $risk->risk_level_id !== null &&
            $risk->tasks()->incomplete()->doesntExist() &&
            (
                $user->is_admin || $risk->isResponsibilityOf($user)
            );
    }

    public function createTasks(User $user, Risk $risk): bool
    {
        return
            $risk->risk_level_id !== null &&
            (
                $user->is_admin || $risk->isResponsibilityOf($user)
            );
    }
}
