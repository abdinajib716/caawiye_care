# Quick Guide: Adding New Permissions

## TL;DR - Quick Steps

```bash
# 1. Create your permission seeder (see template below)
# 2. Run the seeder
php artisan db:seed --class=YourFeaturePermissionSeeder

# 3. Verify it worked
php artisan tinker
>>> App\Models\User::first()->can('your.permission')
=> true

# 4. If permissions don't work, clear cache:
php artisan permission:clear-cache --force
```

## Permission Seeder Template

Copy this template when creating new permissions:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class YourFeaturePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define your permissions
        $permissions = [
            'feature.view',
            'feature.create',
            'feature.edit',
            'feature.delete',
        ];

        // 2. Clear cache BEFORE creating (important!)
        $this->command->info('Clearing permission cache...');
        \Artisan::call('permission:clear-cache', ['--force' => true]);
        
        // 3. Create permissions with group_name
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['group_name' => 'feature'] // Use your feature name
            );
        }

        // 4. Clear cache after creating
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 5. Assign to Superadmin role
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // 6. Assign to other roles if needed
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(['feature.view', 'feature.create']);
        }

        // 7. Final cache clear
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Feature permissions created successfully.');
    }
}
```

## Important Rules

### ✅ DO:
- Use `Spatie\Permission\Models\Permission` (not `App\Models\Permission`)
- Always set `group_name` when creating permissions
- Clear cache before AND after creating permissions
- Use `givePermissionTo()` to assign permissions to roles
- Test permissions after creating them

### ❌ DON'T:
- Don't forget to set `guard_name` to 'web'
- Don't skip cache clearing steps
- Don't use `syncWithoutDetaching()` - use `givePermissionTo()` instead
- Don't create permissions without a `group_name`

## Permission Naming Convention

Follow this pattern: `resource.action`

**Examples**:
- `hospital.view` - View hospitals
- `hospital.create` - Create new hospital
- `hospital.edit` - Edit existing hospital
- `hospital.delete` - Delete hospital
- `hospital.restore` - Restore soft-deleted hospital
- `hospital.force_delete` - Permanently delete hospital

## Adding Menu Items

After creating permissions, add menu items in `app/Services/MenuService/AdminMenuService.php`:

```php
// Your Feature Management
$this->addMenuItem([
    'label' => __('Your Feature'),
    'icon' => 'lucide:icon-name',
    'id' => 'feature-submenu',
    'active' => Route::is('admin.feature.*'),
    'priority' => 20, // Use integer, not float!
    'permissions' => 'feature.view',
    'children' => [
        [
            'label' => __('All Items'),
            'route' => route('admin.feature.index'),
            'active' => Route::is('admin.feature.index') || Route::is('admin.feature.show'),
            'priority' => 10,
            'permissions' => 'feature.view',
        ],
        [
            'label' => __('Add New'),
            'route' => route('admin.feature.create'),
            'active' => Route::is('admin.feature.create'),
            'priority' => 20,
            'permissions' => 'feature.create',
        ],
    ],
]);
```

**Important**: Use integer for `priority`, not float (e.g., use `20`, not `20.5`)

## Troubleshooting

### Problem: New permissions don't work

**Solution**:
```bash
php artisan permission:clear-cache --force
```

### Problem: Menu doesn't show in sidebar

**Checklist**:
1. ✓ Permission exists in database
2. ✓ Permission assigned to user's role
3. ✓ Cache cleared
4. ✓ User logged out and back in
5. ✓ Browser cache cleared

**Test**:
```bash
php artisan tinker
>>> $user = App\Models\User::first();
>>> $user->can('your.permission')
=> true  # Should be true
```

### Problem: Cache won't clear

**Solution**:
```bash
# Try with sudo
sudo php artisan permission:clear-cache --force

# Or manually
sudo rm -rf storage/framework/cache/data/*
php artisan cache:clear
```

## Testing Permissions

### In Tinker:
```bash
php artisan tinker

# Check if user has permission
>>> $user = App\Models\User::first();
>>> $user->can('feature.view')
=> true

# Check all permissions for a role
>>> $role = Spatie\Permission\Models\Role::where('name', 'Superadmin')->first();
>>> $role->permissions->pluck('name')->toArray()
=> [...]

# Check permission cache
>>> $registrar = app(Spatie\Permission\PermissionRegistrar::class);
>>> $registrar->getPermissions()->count()
=> 54
```

### In Blade Views:
```blade
@can('feature.view')
    <a href="{{ route('admin.feature.index') }}">View Features</a>
@endcan
```

### In Controllers:
```php
// Using authorize
$this->authorize('feature.view');

// Using can
if (auth()->user()->can('feature.view')) {
    // ...
}

// Using Gate
if (Gate::allows('feature.view')) {
    // ...
}
```

## Common Mistakes

### 1. Forgetting group_name
```php
// ❌ Wrong
Permission::firstOrCreate(['name' => 'feature.view']);

// ✅ Correct
Permission::firstOrCreate(
    ['name' => 'feature.view', 'guard_name' => 'web'],
    ['group_name' => 'feature']
);
```

### 2. Using float for menu priority
```php
// ❌ Wrong
'priority' => 16.5,

// ✅ Correct
'priority' => 17,
```

### 3. Not clearing cache
```php
// ❌ Wrong - no cache clearing
Permission::create(['name' => 'feature.view']);
$role->givePermissionTo('feature.view');

// ✅ Correct - clear cache before and after
\Artisan::call('permission:clear-cache', ['--force' => true]);
Permission::create(['name' => 'feature.view']);
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$role->givePermissionTo('feature.view');
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

## Need Help?

1. Read the full documentation: `PERMISSION-CACHE-FIX.md`
2. Check Spatie docs: https://spatie.be/docs/laravel-permission
3. Run diagnostics: `php artisan permission:clear-cache`

## Summary

1. Copy the seeder template
2. Define your permissions
3. Run the seeder
4. Add menu items
5. Test permissions
6. Clear browser cache and re-login

That's it! 🎉

