<?php

namespace App\Policies;

use App\Models\User;
use App\Models\System;


class SystemPolicy extends Policy
{
    public function edit(User $user, System $system): bool
    {
        return $user->is_admin || $user->can('system.edit');
    }

    public function delete(User $user, System $system): bool
    {
        return $user->is_admin || $user->can('system.delete');
    }
}
