<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class AutoCreatePermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Extract permission name from exception message
            // Message format: "There is no permission named `permission.name` for guard `web`."
            preg_match('/`([^`]+)`/', $e->getMessage(), $matches);

            if (isset($matches[1])) {
                $permissionName = $matches[1];

                // Check if permission already exists (race condition)
                $existingPermission = Permission::where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->first();

                if (!$existingPermission) {
                    // Auto-create the missing permission
                    Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ]);

                    Log::info("Auto-created missing permission: {$permissionName}");
                }

                // Clear permission cache
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                // Retry the request
                return $this->handle($request, $next);
            }

            // If we couldn't extract the permission name, rethrow
            throw $e;
        } catch (\Throwable $e) {
            // Check if error message contains permission-related error
            if (str_contains($e->getMessage(), 'There is no permission named')) {
                preg_match('/`([^`]+)`/', $e->getMessage(), $matches);

                if (isset($matches[1])) {
                    $permissionName = $matches[1];

                    // Check if permission already exists
                    $existingPermission = Permission::where('name', $permissionName)
                        ->where('guard_name', 'web')
                        ->first();

                    if (!$existingPermission) {
                        // Auto-create the missing permission
                        Permission::create([
                            'name' => $permissionName,
                            'guard_name' => 'web',
                        ]);

                        Log::info("Auto-created missing permission: {$permissionName}");
                    }

                    // Clear permission cache
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                    // Retry the request
                    return $this->handle($request, $next);
                }
            }

            // Rethrow if not permission-related
            throw $e;
        }
    }
}

