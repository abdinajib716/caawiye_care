<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Hospital;
use App\Models\User;

class HospitalPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any hospitals.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('hospital.view');
    }

    /**
     * Determine whether the user can view the hospital.
     */
    public function view(User $user, Hospital $hospital): bool
    {
        return $user->can('hospital.view');
    }

    /**
     * Determine whether the user can create hospitals.
     */
    public function create(User $user): bool
    {
        return $user->can('hospital.create');
    }

    /**
     * Determine whether the user can update the hospital.
     */
    public function update(User $user, Hospital $hospital): bool
    {
        return $user->can('hospital.edit');
    }

    /**
     * Determine whether the user can delete the hospital.
     */
    public function delete(User $user, Hospital $hospital): bool
    {
        return $user->can('hospital.delete');
    }

    /**
     * Determine whether the user can restore the hospital.
     */
    public function restore(User $user, Hospital $hospital): bool
    {
        return $user->can('hospital.restore');
    }

    /**
     * Determine whether the user can permanently delete the hospital.
     */
    public function forceDelete(User $user, Hospital $hospital): bool
    {
        return $user->can('hospital.force_delete');
    }
}

