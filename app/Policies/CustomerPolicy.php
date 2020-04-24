<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy extends Policy
{
    public function index(User $user): bool
    {
        return $user->is_admin || $user->can('customer.show');
    }

    public function create(User $user): bool
    {
        return $user->is_admin || $user->can('customer.create');
    }

    public function edit(User $user, Customer $customer): bool
    {
        return $user->is_admin || $user->can('customer.edit');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->is_admin || $user->can('customer.show');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->is_admin || $user->can('customer.delete');
    }
}
