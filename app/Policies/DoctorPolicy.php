<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;

class DoctorPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any doctors.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('doctor.view');
    }

    /**
     * Determine whether the user can view the doctor.
     */
    public function view(User $user, Doctor $doctor): bool
    {
        return $user->can('doctor.view');
    }

    /**
     * Determine whether the user can create doctors.
     */
    public function create(User $user): bool
    {
        return $user->can('doctor.create');
    }

    /**
     * Determine whether the user can update the doctor.
     */
    public function update(User $user, Doctor $doctor): bool
    {
        return $user->can('doctor.edit');
    }

    /**
     * Determine whether the user can delete the doctor.
     */
    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->can('doctor.delete');
    }

    /**
     * Determine whether the user can restore the doctor.
     */
    public function restore(User $user, Doctor $doctor): bool
    {
        return $user->can('doctor.restore');
    }

    /**
     * Determine whether the user can permanently delete the doctor.
     */
    public function forceDelete(User $user, Doctor $doctor): bool
    {
        return $user->can('doctor.force_delete');
    }
}

