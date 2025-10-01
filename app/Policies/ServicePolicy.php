<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any services.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('service.view');
    }

    /**
     * Determine whether the user can view the service.
     */
    public function view(User $user, Service $service): bool
    {
        return $user->can('service.view');
    }

    /**
     * Determine whether the user can create services.
     */
    public function create(User $user): bool
    {
        return $user->can('service.create');
    }

    /**
     * Determine whether the user can update the service.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->can('service.edit');
    }

    /**
     * Determine whether the user can delete the service.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->can('service.delete');
    }

    /**
     * Determine whether the user can restore the service.
     */
    public function restore(User $user, Service $service): bool
    {
        return $user->can('service.restore');
    }

    /**
     * Determine whether the user can permanently delete the service.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return $user->can('service.force_delete');
    }

    /**
     * Determine whether the user can bulk delete services.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('service.delete');
    }

    /**
     * Determine whether the user can bulk update services.
     */
    public function bulkUpdate(User $user): bool
    {
        return $user->can('service.edit');
    }
}
