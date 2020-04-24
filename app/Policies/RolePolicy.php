<?php

namespace App\Policies;

use App\Models\{
    User,
    Role
};

class RolePolicy extends Policy
{
    public function edit(User $user, Role $role): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->is_admin;
    }
}
