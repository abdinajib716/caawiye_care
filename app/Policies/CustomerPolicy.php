<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer.view');
    }

    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->can('customer.view');
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->can('customer.create');
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->can('customer.edit');
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('customer.delete');
    }

    /**
     * Determine whether the user can restore the customer.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $user->can('customer.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->can('customer.force_delete');
    }
}
