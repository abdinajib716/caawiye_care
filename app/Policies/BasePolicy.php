<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user): ?bool
    {
        // Super admin can do everything
        if ($user->hasRole('Superadmin')) {
            return true;
        }

        return null;
    }

    /**
     * Check if user has the required permission using Spatie.
     */
    protected function checkPermission(User $user, string $permission): bool
    {
        return $user->can($permission);
    }

    /**
     * Check if user owns the resource.
     */
    protected function userOwnsResource(User $user, $resource): bool
    {
        return $user->id === $resource->user_id;
    }

}
