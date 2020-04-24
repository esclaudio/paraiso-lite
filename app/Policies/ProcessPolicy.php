<?php

namespace App\Policies;

use App\Models\{
    User,
    Process
};

class ProcessPolicy extends Policy
{
    public function edit(User $user, Process $process): bool
    {
        return $user->is_admin || $user->id == $process->responsible_id;
    }

    public function delete(User $user, Process $process): bool
    {
        return $user->is_admin ||
            (
                $user->id == $process->responsible_id &&
                $user->hasPermission('process.delete')
            )
        ;
    }
}
