# Frontend UI/UX Components Guide

## 🎯 Purpose
This document catalogs ALL centralized UI components and patterns. **Always check this guide BEFORE creating new components** to prevent redundant implementations.

---

## 📋 Quick Reference

### ⚠️ Already Included Globally - DO NOT Add Again:
1. `<x-toast-notifications />` - Auto-included in backend layout
2. `<x-page-loader />` - Auto-included in backend layout

### ✅ Reusable Components:
1. `<x-card>` - Card wrapper
2. `<x-layouts.backend-layout>` - Backend layout
3. `<livewire:datatable.{name}-datatable />` - Data tables

---

## 🏗️ Layout Components

### Backend Layout
```blade
<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Your content -->
</x-layouts.backend-layout>
```

**Auto-includes:** Toast notifications, page loader, sidebar, header

---

## 🪟 Modal Pattern (Standard)

**⚠️ USE THIS EXACT PATTERN FOR ALL MODALS**

```blade
<div x-data="{ modalOpen: false }">
    <button @click="modalOpen = true">Open Modal</button>

    <div 
        x-show="modalOpen" 
        x-cloak
        class="fixed inset-0 z-[9999]"
    >
        <div class="flex min-h-screen items-center justify-center px-4 py-6">
            <!-- Backdrop -->
            <div 
                @click="modalOpen = false"
                class="fixed inset-0 bg-gray-900/75 dark:bg-gray-900/90"
            ></div>
            
            <!-- Modal -->
            <div class="relative z-10 w-full max-w-2xl rounded-lg bg-white dark:bg-gray-800 shadow-xl">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Title
                        </h3>
                        <button @click="modalOpen = false">
                            <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    Content
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex gap-3 justify-end">
                    <button @click="modalOpen = false" class="btn-secondary">Close</button>
                    <button class="btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Key Requirements:**
- ✅ `z-[9999]` for modal
- ✅ Alpine.js `x-data`, `x-show`, `x-cloak`
- ✅ Dark mode classes
- ✅ Close on backdrop click

**Examples:**
- `resources/views/backend/pages/appointments/show.blade.php`
- `resources/views/backend/pages/action-logs/partials/detail-info.blade.php`

---

## 📊 Statistics Cards Pattern

**⚠️ USE THIS PATTERN FOR DASHBOARD STATS**

```blade
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-card class="bg-white dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                    <iconify-icon icon="lucide:users" class="h-5 w-5"></iconify-icon>
                </div>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($count) }}
                </div>
            </div>
        </div>
    </x-card>
</div>
```

**Grid Options:**
- 2 cards: `lg:grid-cols-2`
- 3 cards: `lg:grid-cols-3`
- 4 cards: `lg:grid-cols-4`
- 5 cards: `lg:grid-cols-5`

**Standard Colors:**
- Blue `bg-blue-500` - Primary/Total
- Green `bg-green-500` - Success/Confirmed
- Yellow `bg-yellow-500` - Pending/Warning
- Red `bg-red-500` - Cancelled/Error
- Purple `bg-purple-500` - Completed
- Orange `bg-orange-500` - Processing

**Examples:**
- `resources/views/backend/pages/appointments/index.blade.php`
- `resources/views/backend/pages/orders/index.blade.php`

---

## 🔔 Toast Notifications

**⚠️ Already included in layout - DO NOT add manually**

### Livewire Usage:
```php
$this->dispatch('show-toast', [
    'message' => __('Success'),
    'type' => 'success', // success, error, warning, info
]);
```

### Controller Usage:
```php
return redirect()->back()->with('success', __('Success'));
return redirect()->back()->with('error', __('Error'));
```

---

## ⏳ Page Loader

**⚠️ Already included in layout - DO NOT add manually**

Auto-shows on:
- Link clicks
- Form submissions
- Livewire requests

---

## 🎨 Icon System

**⚠️ USE ICONIFY ONLY - Lucide Icon Set**

```blade
<iconify-icon icon="lucide:user" class="w-5 h-5"></iconify-icon>
```

**Common Icons:**
```
lucide:user, lucide:users, lucide:eye, lucide:edit
lucide:trash, lucide:plus, lucide:check-circle, lucide:x-circle
lucide:clock, lucide:calendar, lucide:search, lucide:filter
lucide:download, lucide:upload, lucide:menu, lucide:x
```

**Icon Sizes:**
- `w-3 h-3` - Extra small
- `w-4 h-4` - Small
- `w-5 h-5` - Medium (default)
- `w-6 h-6` - Large
- `w-8 h-8`, `w-12 h-12`, `w-16 h-16` - Extra large

---

## 🌙 Dark Mode Pattern

**⚠️ ALWAYS ADD DARK MODE TO EVERY COMPONENT**

```blade
<!-- Backgrounds -->
bg-white dark:bg-gray-800
bg-gray-50 dark:bg-gray-900
bg-gray-100 dark:bg-gray-700

<!-- Text -->
text-gray-900 dark:text-white
text-gray-700 dark:text-gray-300
text-gray-500 dark:text-gray-400

<!-- Borders -->
border-gray-300 dark:border-gray-600
border-gray-200 dark:border-gray-700

<!-- Inputs -->
border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
```

---

## 📝 Form Patterns

### Input Field:
```blade
<input 
    type="text"
    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
>
```

### Textarea:
```blade
<textarea
    rows="4"
    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
></textarea>
```

### Select:
```blade
<select class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    <option>Option 1</option>
</select>
```

---

## 🏷️ Badge Patterns

```blade
<!-- Success -->
<span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
    <iconify-icon icon="lucide:check-circle" class="h-3.5 w-3.5"></iconify-icon>
    Success
</span>

<!-- Warning -->
<span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800">
    <iconify-icon icon="lucide:clock" class="h-3.5 w-3.5"></iconify-icon>
    Pending
</span>

<!-- Error -->
<span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
    <iconify-icon icon="lucide:x-circle" class="h-3.5 w-3.5"></iconify-icon>
    Failed
</span>

<!-- Info -->
<span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800">
    <iconify-icon icon="lucide:info" class="h-3.5 w-3.5"></iconify-icon>
    Info
</span>
```

---

## 🎯 Button Patterns

```blade
<!-- Primary -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
    Save
</button>

<!-- Secondary -->
<button class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
    Cancel
</button>

<!-- Danger -->
<button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
    Delete
</button>

<!-- Icon Button -->
<button class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-800">
    <iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>
</button>
```

---

## 📊 Datatable Pattern

**⚠️ USE LIVEWIRE COMPONENTS - NO MANUAL TABLES**

```blade
<x-card class="bg-white dark:bg-gray-800">
    <livewire:datatable.your-datatable lazy />
</x-card>
```

See `AI-CODING-GUIDELINES.md` for datatable component creation.

---

## 📱 Responsive Grid Pattern

```blade
<!-- 1 column mobile, 2 tablet, 4 desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

<!-- 1 column mobile, 3 desktop -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- Hide on mobile -->
<div class="hidden md:block">Desktop only</div>

<!-- Show only mobile -->
<div class="block md:hidden">Mobile only</div>
```

---

## 📦 Empty State Pattern

```blade
<div class="text-center py-12">
    <iconify-icon icon="lucide:inbox" class="w-16 h-16 text-gray-400 mx-auto mb-4"></iconify-icon>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No items found</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by creating a new item.</p>
    @can('resource.create')
        <a href="{{ route('admin.resource.create') }}" class="btn-primary">Create New</a>
    @endcan
</div>
```

---

## 🚫 DO NOT Create (Already Exist)

1. ❌ Page loader
2. ❌ Toast notifications
3. ❌ New modal patterns
4. ❌ Custom icon systems
5. ❌ Card components
6. ❌ Manual datatables
7. ❌ New statistics patterns
8. ❌ Custom loading spinners
9. ❌ New badge styles
10. ❌ Custom empty states

---

## ✅ Pre-Implementation Checklist

Before creating ANY UI component:

- [ ] Checked this guide for existing pattern
- [ ] Searched codebase for similar implementation
- [ ] Added dark mode support
- [ ] Made responsive (mobile, tablet, desktop)
- [ ] Used Iconify icons (lucide set)
- [ ] Followed color patterns
- [ ] Used existing spacing/sizing
- [ ] Tested in light and dark mode
- [ ] Verified on mobile devices

---

## 📚 Reference Examples

**Modals:**
- `resources/views/backend/pages/appointments/show.blade.php`
- `resources/views/backend/pages/action-logs/partials/detail-info.blade.php`

**Statistics:**
- `resources/views/backend/pages/appointments/index.blade.php`
- `resources/views/backend/pages/orders/index.blade.php`

**Forms:**
- `resources/views/backend/pages/appointments/create.blade.php`

**Datatables:**
- `app/Livewire/Datatable/*Datatable.php`

---

## 📝 Customer Selection Pattern (Booking Forms)

**⚠️ USE THIS EXACT PATTERN FOR ALL BOOKING FORMS**

### Standard Customer Search & Selection

```blade
<div class="space-y-6">
    <!-- Customer Search -->
    <div>
        <label class="form-label">{{ __('Search Customer') }}</label>
        <input type="text" wire:model.live.debounce.300ms="customerSearch" class="form-control" placeholder="{{ __('Search by name or phone number') }}">
    </div>

    <!-- Matching Customers List -->
    @if (!empty($matchingCustomers))
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg divide-y dark:divide-gray-700">
            @foreach ($matchingCustomers as $customer)
                <button type="button" wire:click="selectCustomer({{ $customer['id'] }})" class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer['phone'] }}</div>
                </button>
            @endforeach
        </div>
    @endif

    <!-- Selected Customer Display -->
    @if ($customerId)
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <div class="flex items-start">
                <iconify-icon icon="lucide:check-circle" class="h-5 w-5 mt-0.5 text-green-600 dark:text-green-400"></iconify-icon>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-green-900 dark:text-green-300">{{ __('Customer Selected') }}</h4>
                    <p class="text-sm mt-1 text-green-700 dark:text-green-400">{{ $customerName }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
```

### Key Requirements:
- ✅ Use `form-label` class for labels
- ✅ Use `form-control` class for inputs  
- ✅ Use `<button>` elements (not `<div>`) for customer list items
- ✅ Use `wire:click` for selection (not `@click`)
- ✅ Include hover states: `hover:bg-gray-50 dark:hover:bg-gray-800`
- ✅ Use dividers: `divide-y dark:divide-gray-700`
- ✅ Green success state for selected customer

### Used In:
- Medicine Booking Form ✅
- Appointment Booking Form ✅
- Lab Test Booking Form ✅
- Scan & Imaging Booking Form ✅

---

## 🔍 Need Help?

1. Check similar existing pages
2. Review this guide
3. Search codebase for patterns
4. Check `AI-CODING-GUIDELINES.md`
5. Ask before creating new patterns

**Remember:** Reuse > Recreate
