# Quick Guide: Adding New Permissions

## ⚠️ CRITICAL WARNING

**NEVER use `syncPermissions()` in permission seeders!**

❌ **WRONG - This REPLACES all existing permissions:**
```php
$role->syncPermissions($newPermissions); // ❌ DELETES ALL OLD PERMISSIONS!
```

✅ **CORRECT - This ADDS new permissions:**
```php
foreach ($permissions as $permission) {
    if (!$role->hasPermissionTo($permission)) {
        $role->givePermissionTo($permission); // ✅ ADDS WITHOUT REMOVING OLD ONES
    }
}
```

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

        // 5. Assign to Superadmin role (CRITICAL: Use givePermissionTo, NOT syncPermissions!)
        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $permissionModels = Permission::whereIn('name', $permissions)->get();
            foreach ($permissionModels as $permission) {
                // Only add if not already assigned (prevents duplicates)
                if (!$superAdminRole->hasPermissionTo($permission)) {
                    $superAdminRole->givePermissionTo($permission);
                }
            }
        }

        // 6. Assign to other roles if needed
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $viewPermissions = Permission::whereIn('name', ['feature.view', 'feature.create'])->get();
            foreach ($viewPermissions as $permission) {
                if (!$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                }
            }
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
- **DON'T use `syncPermissions()`** - It DELETES all existing permissions and only keeps the new ones!
- **DON'T use `syncWithoutDetaching()`** - Use `givePermissionTo()` instead
- Don't forget to set `guard_name` to 'web'
- Don't skip cache clearing steps
- Don't create permissions without a `group_name`
- Don't forget to check `hasPermissionTo()` before adding (prevents duplicates)

## Permission Naming Convention

Follow this pattern: `resource.action`

**Examples**:
- `hospital.view` - View hospitals
- `hospital.create` - Create new hospital
- `hospital.edit` - Edit existing hospital
- `hospital.delete` - Delete hospital (single or bulk)
- `hospital.restore` - Restore soft-deleted hospital
- `hospital.force_delete` - Permanently delete hospital

**Note:** The `.delete` permission covers both single and bulk delete operations. No need for separate `hospital.bulk_delete` permission.

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

### In Livewire Components (Bulk Actions):
```php
public function bulkDelete(): void
{
    // ALWAYS authorize bulk operations
    $this->authorize('transaction.delete');
    
    $ids = $this->selectedItems;
    
    if (empty($ids)) {
        $this->dispatch('show-toast', [
            'message' => __('No items selected'),
            'type' => 'warning',
        ]);
        return;
    }
    
    try {
        $deletedCount = Model::whereIn('id', $ids)->delete();
        
        if ($deletedCount === 0) {
            $this->dispatch('show-toast', [
                'message' => __('No items were deleted. Selected items may include protected records.'),
                'type' => 'error',
            ]);
        } else {
            $this->dispatch('show-toast', [
                'message' => __(':count items deleted successfully', ['count' => $deletedCount]),
                'type' => 'success',
            ]);
        }
    } catch (\Exception $e) {
        $this->dispatch('show-toast', [
            'message' => __('Failed to delete: :error', ['error' => $e->getMessage()]),
            'type' => 'error',
        ]);
    }
    
    // CRITICAL: Always reset selection after bulk operations
    $this->selectedItems = [];
    $this->dispatch('resetSelectedItems');
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

## Bulk Action Permissions

### Important: Use Same Permission for Single & Bulk Delete

```php
// ❌ WRONG - Don't create separate bulk permissions
'feature.delete',
'feature.bulk_delete',  // NOT NEEDED!

// ✅ CORRECT - One permission covers both
'feature.delete'  // Covers single AND bulk delete
```

### Authorize Bulk Actions in Livewire Components

**Always add authorization to bulk action methods:**

```php
public function bulkDelete(): void
{
    // REQUIRED: Check permission
    $this->authorize('resource.delete');
    
    // ... rest of bulk delete logic
}

public function bulkStatusUpdate(string $status): void
{
    // REQUIRED: Check permission
    $this->authorize('resource.edit');
    
    // ... rest of bulk update logic
}
```

### Common Bulk Actions:

| Action | Permission | Authorization |
|--------|-----------|---------------|
| Bulk Delete | `resource.delete` | `$this->authorize('resource.delete')` |
| Bulk Update | `resource.edit` | `$this->authorize('resource.edit')` |
| Bulk Activate | `resource.edit` | `$this->authorize('resource.edit')` |
| Bulk Export | `resource.view` | `$this->authorize('resource.view')` |

## 🔧 Emergency: Restore Missing Permissions

If you accidentally used `syncPermissions()` and lost permissions, run this to restore ALL permissions to Superadmin:

```bash
php artisan tinker
```

Then run:
```php
$role = Spatie\Permission\Models\Role::where('name', 'Superadmin')->first();
$all = Spatie\Permission\Models\Permission::all();
$current = $role->permissions->pluck('name')->toArray();
$added = 0;
foreach($all as $perm) {
    if (!in_array($perm->name, $current)) {
        $role->givePermissionTo($perm);
        echo "✓ Added: {$perm->name}\n";
        $added++;
    }
}
echo "\n✅ Added {$added} missing permissions\n";
echo "Superadmin now has: " . $role->fresh()->permissions->count() . " permissions\n";
exit
```

Then clear cache:
```bash
sudo php artisan permission:clear-cache --force
```

## Summary

1. ⚠️  **NEVER use `syncPermissions()`** - Always use `givePermissionTo()` with `hasPermissionTo()` check
2. Copy the seeder template above (it has the correct pattern)
3. Define your permissions
4. Run the seeder
5. Add menu items
6. **Add authorization to bulk actions**
7. Test permissions
8. Clear browser cache and re-login

**Remember:** `syncPermissions()` = DISASTER. Use `givePermissionTo()` = SAFE ✅

That's it! 🎉

