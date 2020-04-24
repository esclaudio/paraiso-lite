<?php

namespace App\Policies;

use App\Models\{
    User,
    Product
};

class ProductPolicy extends Policy
{
    public function edit(User $user, Product $product): bool
    {
        return $user->is_admin || $user->hasPermission('product.edit');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->is_admin || $user->hasPermission('product.delete');
    }
}
