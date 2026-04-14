<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentTransactionPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'transaction.view');
    }

    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, PaymentTransaction $transaction): bool
    {
        return $this->checkPermission($user, 'transaction.view');
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'transaction.create');
    }

    /**
     * Determine whether the user can update the transaction.
     */
    public function update(User $user, PaymentTransaction $transaction): bool
    {
        return $this->checkPermission($user, 'transaction.edit');
    }

    /**
     * Determine whether the user can delete the transaction.
     */
    public function delete(User $user, PaymentTransaction $transaction): bool
    {
        return $this->checkPermission($user, 'transaction.delete');
    }

    /**
     * Determine whether the user can restore the transaction.
     */
    public function restore(User $user, PaymentTransaction $transaction): bool
    {
        return $this->checkPermission($user, 'transaction.delete');
    }

    /**
     * Determine whether the user can permanently delete the transaction.
     */
    public function forceDelete(User $user, PaymentTransaction $transaction): bool
    {
        return $this->checkPermission($user, 'transaction.delete');
    }
}
