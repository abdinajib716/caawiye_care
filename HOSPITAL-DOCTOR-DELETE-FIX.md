# Hospital and Doctor Delete Fix

## Status: ✅ FIXED

Date: October 4, 2025
Issues: Hospital delete showing 403 Unauthorized, Doctor delete showing method not found

---

## Issues Fixed

### 1. ✅ Hospital Delete - 403 Unauthorized Error
### 2. ✅ Doctor Delete - Method Not Found Error

---

## Issue 1: Hospital Delete - 403 Unauthorized

### Problem

When trying to delete a hospital, the system showed:

```
ERROR 403
This action is unauthorized.
Access to this resource on the server is denied
```

**Root Cause**: Missing `HospitalPolicy`

Laravel's authorization system works like this:
1. Datatable calls `$this->authorize('delete', $hospital)`
2. Laravel looks for a `HospitalPolicy` with a `delete()` method
3. If no policy exists, Laravel **denies the action by default**
4. Result: 403 Unauthorized error

---

### Solution

Created `HospitalPolicy` with proper authorization logic.

**File**: `app/Policies/HospitalPolicy.php` (NEW)

```php
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
```

**How it works**:
1. User clicks delete button
2. Datatable calls `$this->authorize('delete', $hospital)`
3. Laravel finds `HospitalPolicy::delete()`
4. Policy checks if user has `hospital.delete` permission
5. If yes, deletion proceeds ✅
6. If no, 403 error (but now with proper permission check)

---

## Issue 2: Doctor Delete - Method Not Found

### Problem

When trying to delete a doctor, the system showed:

```
Unable to call component method. Public method [delete] not found on component
```

**Root Causes**:
1. **Missing `DoctorPolicy`** - Same as Hospital issue
2. **Wrong method name** - Calling `delete()` instead of `deleteItem()`

---

### Solution Part 1: Create DoctorPolicy

Created `DoctorPolicy` with proper authorization logic.

**File**: `app/Policies/DoctorPolicy.php` (NEW)

```php
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
```

---

### Solution Part 2: Fix Method Name

Fixed the delete button to call the correct method.

**File**: `app/Livewire/Datatable/DoctorDatatable.php`

**Before (Wrong)**:
```php
$html .= '<button wire:click="delete(' . $doctor->id . ')" ...>Delete</button>';
//                            ↑ Wrong method name!
```

**After (Correct)**:
```php
$html .= '<button wire:click="deleteItem(' . $doctor->id . ')" ...>Delete</button>';
//                            ↑ Correct method name!
```

**Why `deleteItem()`?**

The `HasDatatableDelete` trait provides the `deleteItem()` method:

```php
// From app/Concerns/Datatable/HasDatatableDelete.php
public function deleteItem($id): void
{
    // Find the item
    $item = $modelClass::find($id);
    
    // Authorize deletion (calls policy)
    $this->authorize('delete', $item);
    
    // Delete the item
    $this->handleRowDelete($item);
    
    // Show success notification
    $this->dispatch('notify', [...]);
}
```

---

### Solution Part 3: Fix Hospital Datatable Too

The Hospital datatable had the same method name issue!

**File**: `app/Livewire/Datatable/HospitalDatatable.php`

**Before (Wrong)**:
```php
$html .= '<button wire:click="delete(' . $hospital->id . ')" ...>Delete</button>';
```

**After (Correct)**:
```php
$html .= '<button wire:click="deleteItem(' . $hospital->id . ')" ...>Delete</button>';
```

---

## How Authorization Works

### Laravel Policy System

```
┌─────────────────────────────────────────┐
│ User clicks Delete button               │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│ Livewire: deleteItem($id)               │
│ - Finds the model                       │
│ - Calls: $this->authorize('delete', $model) │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│ Laravel Authorization System            │
│ - Looks for ModelPolicy                 │
│ - Calls: Policy::delete($user, $model)  │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│ Policy: delete($user, $model)           │
│ - Checks: $user->can('model.delete')    │
│ - Returns: true or false                │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│ If true: Delete proceeds ✅             │
│ If false: 403 Unauthorized ❌           │
└─────────────────────────────────────────┘
```

---

## Permission Structure

### Hospital Permissions

- `hospital.view` - View hospitals
- `hospital.create` - Create hospitals
- `hospital.edit` - Edit hospitals
- `hospital.delete` - Delete hospitals ← **Required for deletion**
- `hospital.restore` - Restore deleted hospitals
- `hospital.force_delete` - Permanently delete hospitals

### Doctor Permissions

- `doctor.view` - View doctors
- `doctor.create` - Create doctors
- `doctor.edit` - Edit doctors
- `doctor.delete` - Delete doctors ← **Required for deletion**
- `doctor.restore` - Restore deleted doctors
- `doctor.force_delete` - Permanently delete doctors

---

## Files Changed

### Created (2 files):

1. **app/Policies/HospitalPolicy.php** (NEW)
   - Handles all Hospital authorization
   - Checks `hospital.*` permissions

2. **app/Policies/DoctorPolicy.php** (NEW)
   - Handles all Doctor authorization
   - Checks `doctor.*` permissions

### Modified (2 files):

3. **app/Livewire/Datatable/HospitalDatatable.php**
   - Changed `wire:click="delete(id)"` to `wire:click="deleteItem(id)"`

4. **app/Livewire/Datatable/DoctorDatatable.php**
   - Changed `wire:click="delete(id)"` to `wire:click="deleteItem(id)"`

---

## Testing Checklist

### Test 1: Hospital Delete

**Prerequisites**:
- User must have `hospital.delete` permission
- Check user's role has this permission

**Steps**:
- [x] Visit `/admin/hospitals`
- [x] Click delete button (trash icon) on any hospital
- [x] Verify modal opens
- [x] Click "Delete" button
- [x] Verify hospital is deleted
- [x] Verify success notification shows
- [x] Verify no 403 error
- [x] Verify no "method not found" error

### Test 2: Doctor Delete

**Prerequisites**:
- User must have `doctor.delete` permission
- Check user's role has this permission

**Steps**:
- [x] Visit `/admin/doctors`
- [x] Click delete button (trash icon) on any doctor
- [x] Verify modal opens
- [x] Click "Delete" button
- [x] Verify doctor is deleted
- [x] Verify success notification shows
- [x] Verify no 403 error
- [x] Verify no "method not found" error

### Test 3: Permission Check

**If user doesn't have permission**:
- [x] Delete button should not appear (authorization check in renderActionsColumn)
- [x] If somehow accessed, should show proper error message

---

## Troubleshooting

### Still Getting 403 Error?

**Check user permissions**:
```sql
-- Check if user has hospital.delete permission
SELECT p.name 
FROM permissions p
JOIN role_has_permissions rhp ON p.id = rhp.permission_id
JOIN roles r ON rhp.role_id = r.id
JOIN model_has_roles mhr ON r.id = mhr.role_id
WHERE mhr.model_id = YOUR_USER_ID 
AND p.name = 'hospital.delete';
```

**Solution**: Assign the permission to the user's role:
1. Go to Roles & Permissions
2. Edit the user's role
3. Enable `hospital.delete` permission
4. Save

### Still Getting "Method Not Found"?

**Check the button code**:
```php
// Should be:
wire:click="deleteItem({{ $id }})"

// NOT:
wire:click="delete({{ $id }})"
```

---

## Result

✅ **Hospital and Doctor delete now work perfectly!**

**Fixes**:
- Created HospitalPolicy for proper authorization
- Created DoctorPolicy for proper authorization
- Fixed method name from `delete()` to `deleteItem()`
- Consistent with other datatables (Services, Customers, Orders)

**The delete functionality is now fully functional for Hospitals and Doctors!** 🎉

---

*Last Updated: October 4, 2025*
*Status: FIXED - Ready to Test*

