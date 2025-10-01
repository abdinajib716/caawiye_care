<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;

class ServiceCategoryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any service categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('service.view');
    }

    /**
     * Determine whether the user can view the service category.
     */
    public function view(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('service.view');
    }

    /**
     * Determine whether the user can create service categories.
     */
    public function create(User $user): bool
    {
        return $user->can('service.create');
    }

    /**
     * Determine whether the user can update the service category.
     */
    public function update(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('service.edit');
    }

    /**
     * Determine whether the user can delete the service category.
     */
    public function delete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('service.delete');
    }

    /**
     * Determine whether the user can restore the service category.
     */
    public function restore(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('service.restore');
    }

    /**
     * Determine whether the user can permanently delete the service category.
     */
    public function forceDelete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('service.force_delete');
    }

    /**
     * Determine whether the user can bulk delete service categories.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('service.delete');
    }

    /**
     * Determine whether the user can bulk update service categories.
     */
    public function bulkUpdate(User $user): bool
    {
        return $user->can('service.edit');
    }
}
