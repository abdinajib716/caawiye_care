<?php

declare(strict_types=1);

namespace App\Services\MenuService;

use App\Enums\Hooks\AdminFilterHook;
use App\Services\Content\ContentService;
use App\Support\Facades\Hook;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminMenuService
{
    /**
     * @var AdminMenuItem[][]
     */
    protected array $groups = [];

    /**
     * Add a menu item to the admin sidebar.
     *
     * @param  AdminMenuItem|array  $item  The menu item or configuration array
     * @param  string|null  $group  The group to add the item to
     *
     * @throws \InvalidArgumentException
     */
    public function addMenuItem(AdminMenuItem|array $item, ?string $group = null): void
    {
        $group = $group ?: __('Main');
        $menuItem = $this->createAdminMenuItem($item);
        if (! isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }

        if ($menuItem->userHasPermission()) {
            $this->groups[$group][] = $menuItem;
        }
    }

    protected function createAdminMenuItem(AdminMenuItem|array $data): AdminMenuItem
    {
        if ($data instanceof AdminMenuItem) {
            return $data;
        }

        $menuItem = new AdminMenuItem();

        if (isset($data['children']) && is_array($data['children'])) {
            $data['children'] = array_map(
                function ($child) {
                    // Check if user is authenticated
                    $user = auth()->user();
                    if (! $user) {
                        return null;
                    }

                    // Handle permissions.
                    if (isset($child['permission'])) {
                        $child['permissions'] = $child['permission'];
                        unset($child['permission']);
                    }

                    $permissions = $child['permissions'] ?? [];
                    if (empty($permissions) || $user->hasAnyPermission((array) $permissions)) {
                        return $this->createAdminMenuItem($child);
                    }

                    return null;
                },
                $data['children']
            );

            // Filter out null values (items without permission).
            $data['children'] = array_filter($data['children']);
        }

        // Convert 'permission' to 'permissions' for consistency
        if (isset($data['permission'])) {
            $data['permissions'] = $data['permission'];
            unset($data['permission']);
        }

        // Handle route with params
        if (isset($data['route']) && isset($data['params'])) {
            $routeName = $data['route'];
            $params = $data['params'];

            if (is_array($params)) {
                $data['route'] = route($routeName, $params);
            } else {
                $data['route'] = route($routeName, [$params]);
            }
        }

        return $menuItem->setAttributes($data);
    }

    public function getMenu()
    {
        $this->addMenuItem([
            'label' => __('Dashboard'),
            'icon' => 'lucide:layout-dashboard',
            'route' => route('admin.dashboard'),
            'active' => Route::is('admin.dashboard'),
            'id' => 'dashboard',
            'priority' => 1,
            'permissions' => 'dashboard.view',
        ]);

        // Services Management - REMOVED

        // Customers Management
        $this->addMenuItem([
            'label' => __('Customers'),
            'icon' => 'lucide:users',
            'id' => 'customers-submenu',
            'active' => Route::is('admin.customers.*'),
            'priority' => 15,
            'permissions' => 'customer.view',
            'children' => [
                [
                    'label' => __('All Customers'),
                    'route' => route('admin.customers.index'),
                    'active' => Route::is('admin.customers.index') || Route::is('admin.customers.show'),
                    'priority' => 10,
                    'permissions' => 'customer.view',
                ],
                [
                    'label' => __('Add Customer'),
                    'route' => route('admin.customers.create'),
                    'active' => Route::is('admin.customers.create'),
                    'priority' => 20,
                    'permissions' => 'customer.create',
                ],
            ],
        ]);

        // Hospitals Management
        $this->addMenuItem([
            'label' => __('Hospitals'),
            'icon' => 'lucide:building-2',
            'id' => 'hospitals-submenu',
            'active' => Route::is('admin.hospitals.*'),
            'priority' => 16,
            'permissions' => 'hospital.view',
            'children' => [
                [
                    'label' => __('All Hospitals'),
                    'route' => route('admin.hospitals.index'),
                    'active' => Route::is('admin.hospitals.index') || Route::is('admin.hospitals.show'),
                    'priority' => 10,
                    'permissions' => 'hospital.view',
                ],
                [
                    'label' => __('Add Hospital'),
                    'route' => route('admin.hospitals.create'),
                    'active' => Route::is('admin.hospitals.create'),
                    'priority' => 20,
                    'permissions' => 'hospital.create',
                ],
            ],
        ]);

        // Doctors Management
        $this->addMenuItem([
            'label' => __('Doctors'),
            'icon' => 'lucide:stethoscope',
            'id' => 'doctors-submenu',
            'active' => Route::is('admin.doctors.*'),
            'priority' => 17,
            'permissions' => 'doctor.view',
            'children' => [
                [
                    'label' => __('All Doctors'),
                    'route' => route('admin.doctors.index'),
                    'active' => Route::is('admin.doctors.index') || Route::is('admin.doctors.show'),
                    'priority' => 10,
                    'permissions' => 'doctor.view',
                ],
                [
                    'label' => __('Add Doctor'),
                    'route' => route('admin.doctors.create'),
                    'active' => Route::is('admin.doctors.create'),
                    'priority' => 20,
                    'permissions' => 'doctor.create',
                ],
            ],
        ]);

        // Appointments Management
        $this->addMenuItem([
            'label' => __('Appointments'),
            'icon' => 'lucide:calendar-check',
            'id' => 'appointments-submenu',
            'active' => Route::is('admin.appointments.*'),
            'priority' => 18,
            'permissions' => 'appointment.view',
            'children' => [
                [
                    'label' => __('Book Appointment'),
                    'route' => route('admin.appointments.create'),
                    'active' => Route::is('admin.appointments.create'),
                    'priority' => 10,
                    'permissions' => 'appointment.create',
                ],
                [
                    'label' => __('All Appointments'),
                    'route' => route('admin.appointments.index'),
                    'active' => Route::is('admin.appointments.index') || Route::is('admin.appointments.show'),
                    'priority' => 20,
                    'permissions' => 'appointment.view',
                ],
            ],
        ]);

        // Medicine Collection Management
        $this->addMenuItem([
            'label' => __('Medicines'),
            'icon' => 'lucide:pill',
            'id' => 'medicines-submenu',
            'active' => Route::is('admin.medicine-orders.*') || Route::is('admin.medicines.*') || Route::is('admin.suppliers.*') || Route::is('admin.delivery-settings.*'),
            'priority' => 19,
            'permissions' => 'medicine_order.view',
            'children' => [
                [
                    'label' => __('Book Medicine'),
                    'route' => route('admin.medicine-orders.create'),
                    'active' => Route::is('admin.medicine-orders.create'),
                    'priority' => 10,
                    'permissions' => 'medicine_order.create',
                ],
                [
                    'label' => __('Medicine Items'),
                    'route' => route('admin.medicines.index'),
                    'active' => Route::is('admin.medicines.*'),
                    'priority' => 20,
                    'permissions' => 'medicine.view',
                ],
                [
                    'label' => __('Suppliers'),
                    'route' => route('admin.suppliers.index'),
                    'active' => Route::is('admin.suppliers.*'),
                    'priority' => 30,
                    'permissions' => 'supplier.view',
                ],
                [
                    'label' => __('Delivery Settings'),
                    'route' => route('admin.delivery-settings.index'),
                    'active' => Route::is('admin.delivery-settings.*'),
                    'priority' => 40,
                    'permissions' => 'delivery_location.view',
                ],
            ],
        ]);

        // Lab Test Booking Management
        $this->addMenuItem([
            'label' => __('Lab Tests'),
            'icon' => 'lucide:flask-conical',
            'id' => 'lab-tests-submenu',
            'active' => Route::is('admin.lab-test-bookings.*') || Route::is('admin.lab-tests.*'),
            'priority' => 20,
            'permissions' => 'lab_test_booking.view',
            'children' => [
                [
                    'label' => __('Book Lab Test'),
                    'route' => route('admin.lab-test-bookings.create'),
                    'active' => Route::is('admin.lab-test-bookings.create'),
                    'priority' => 10,
                    'permissions' => 'lab_test_booking.create',
                ],
                [
                    'label' => __('Lab Test Bookings'),
                    'route' => route('admin.lab-test-bookings.index'),
                    'active' => Route::is('admin.lab-test-bookings.index') || Route::is('admin.lab-test-bookings.show'),
                    'priority' => 20,
                    'permissions' => 'lab_test_booking.view',
                ],
                [
                    'label' => __('Lab Tests'),
                    'route' => route('admin.lab-tests.index'),
                    'active' => Route::is('admin.lab-tests.*'),
                    'priority' => 30,
                    'permissions' => 'lab_test.view',
                ],
            ],
        ]);

        // Scan & Imaging Management
        $this->addMenuItem([
            'label' => __('Scan & Imaging'),
            'icon' => 'lucide:scan',
            'id' => 'scan-imaging-submenu',
            'active' => Route::is('admin.scan-imaging-bookings.*') || Route::is('admin.scan-imaging-services.*'),
            'priority' => 21,
            'permissions' => 'scan_imaging_booking.view',
            'children' => [
                [
                    'label' => __('Book Scan & Imaging'),
                    'route' => route('admin.scan-imaging-bookings.create'),
                    'active' => Route::is('admin.scan-imaging-bookings.create'),
                    'priority' => 10,
                    'permissions' => 'scan_imaging_booking.create',
                ],
                [
                    'label' => __('Scan & Imaging Bookings'),
                    'route' => route('admin.scan-imaging-bookings.index'),
                    'active' => Route::is('admin.scan-imaging-bookings.index') || Route::is('admin.scan-imaging-bookings.show'),
                    'priority' => 20,
                    'permissions' => 'scan_imaging_booking.view',
                ],
                [
                    'label' => __('Services'),
                    'route' => route('admin.scan-imaging-services.index'),
                    'active' => Route::is('admin.scan-imaging-services.*'),
                    'priority' => 30,
                    'permissions' => 'scan_imaging_service.view',
                ],
            ],
        ]);

        // Report Collections Management
        $this->addMenuItem([
            'label' => __('Collections'),
            'icon' => 'lucide:clipboard-list',
            'id' => 'collections-submenu',
            'active' => Route::is('admin.collections.*'),
            'priority' => 22,
            'permissions' => ['report.view', 'collection.view'],
            'children' => [
                [
                    'label' => __('New Request'),
                    'route' => route('admin.collections.create'),
                    'active' => Route::is('admin.collections.create'),
                    'priority' => 10,
                    'permissions' => ['report.view', 'collection.view'],
                ],
                [
                    'label' => __('All Requests'),
                    'route' => route('admin.collections.index'),
                    'active' => Route::is('admin.collections.index') || Route::is('admin.collections.show'),
                    'priority' => 20,
                    'permissions' => ['report.view', 'collection.view'],
                ],
                [
                    'label' => __('Settings'),
                    'route' => route('admin.collections.settings'),
                    'active' => Route::is('admin.collections.settings'),
                    'priority' => 30,
                    'permissions' => ['report.view', 'collection.view'],
                ],
            ],
        ]);

        // Providers Management
        $this->addMenuItem([
            'label' => __('Providers'),
            'icon' => 'lucide:building',
            'id' => 'providers-submenu',
            'active' => Route::is('admin.providers.*'),
            'priority' => 23,
            'permissions' => 'provider.view',
            'children' => [
                [
                    'label' => __('All Providers'),
                    'route' => route('admin.providers.index'),
                    'active' => Route::is('admin.providers.index') || Route::is('admin.providers.show'),
                    'priority' => 10,
                    'permissions' => 'provider.view',
                ],
                [
                    'label' => __('Add Provider'),
                    'route' => route('admin.providers.create'),
                    'active' => Route::is('admin.providers.create'),
                    'priority' => 20,
                    'permissions' => 'provider.create',
                ],
            ],
        ]);

        // Order Zone - REMOVED

        // Orders Management
        $this->addMenuItem([
            'label' => __('Orders'),
            'icon' => 'lucide:shopping-cart',
            'id' => 'orders-submenu',
            'active' => Route::is('admin.orders.*'),
            'priority' => 25,
            'permissions' => 'order.view',
            'children' => [
                [
                    'label' => __('All Orders'),
                    'route' => route('admin.orders.index'),
                    'active' => Route::is('admin.orders.index') || Route::is('admin.orders.show'),
                    'priority' => 10,
                    'permissions' => 'order.view',
                ],
            ],
        ]);

        // Transactions Management
        $this->addMenuItem([
            'label' => __('Transactions'),
            'icon' => 'lucide:credit-card',
            'route' => route('admin.transactions.index'),
            'active' => Route::is('admin.transactions.*'),
            'id' => 'transactions',
            'priority' => 30,
            'permissions' => 'transaction.view',
        ]);

        // Expenses Management
        $this->addMenuItem([
            'label' => __('Expenses'),
            'icon' => 'lucide:receipt',
            'id' => 'expenses-submenu',
            'active' => Route::is('admin.expenses.*') || Route::is('admin.expense-categories.*'),
            'priority' => 32,
            'permissions' => 'expense.view',
            'children' => [
                [
                    'label' => __('All Expenses'),
                    'route' => route('admin.expenses.index'),
                    'active' => Route::is('admin.expenses.index') || Route::is('admin.expenses.show'),
                    'priority' => 10,
                    'permissions' => 'expense.view',
                ],
                [
                    'label' => __('Add Expense'),
                    'route' => route('admin.expenses.create'),
                    'active' => Route::is('admin.expenses.create'),
                    'priority' => 20,
                    'permissions' => 'expense.create',
                ],
                [
                    'label' => __('Categories'),
                    'route' => route('admin.expense-categories.index'),
                    'active' => Route::is('admin.expense-categories.*'),
                    'priority' => 30,
                    'permissions' => 'expense_category.view',
                ],
            ],
        ]);

        // Refunds Management
        $this->addMenuItem([
            'label' => __('Refunds'),
            'icon' => 'lucide:undo-2',
            'id' => 'refunds-submenu',
            'active' => Route::is('admin.refunds.*'),
            'priority' => 33,
            'permissions' => 'refund.view',
            'children' => [
                [
                    'label' => __('All Refunds'),
                    'route' => route('admin.refunds.index'),
                    'active' => Route::is('admin.refunds.index') || Route::is('admin.refunds.show'),
                    'priority' => 10,
                    'permissions' => 'refund.view',
                ],
            ],
        ]);

        // Financial Reports
        $this->addMenuItem([
            'label' => __('Reports'),
            'icon' => 'lucide:bar-chart-3',
            'id' => 'reports-submenu',
            'active' => Route::is('admin.reports.*'),
            'priority' => 35,
            'permissions' => 'report.view',
            'children' => [
                [
                    'label' => __('Overview'),
                    'route' => route('admin.reports.index'),
                    'active' => Route::is('admin.reports.index'),
                    'priority' => 10,
                    'permissions' => 'report.view',
                ],
                [
                    'label' => __('Revenue Report'),
                    'route' => route('admin.reports.revenue'),
                    'active' => Route::is('admin.reports.revenue'),
                    'priority' => 20,
                    'permissions' => 'report.view',
                ],
                [
                    'label' => __('Expense Report'),
                    'route' => route('admin.reports.expenses'),
                    'active' => Route::is('admin.reports.expenses'),
                    'priority' => 30,
                    'permissions' => 'report.view',
                ],
                [
                    'label' => __('Provider Payouts'),
                    'route' => route('admin.reports.provider-payouts'),
                    'active' => Route::is('admin.reports.provider-payouts'),
                    'priority' => 40,
                    'permissions' => 'report.view',
                ],
                [
                    'label' => __('Profit & Loss'),
                    'route' => route('admin.reports.profit-loss'),
                    'active' => Route::is('admin.reports.profit-loss'),
                    'priority' => 50,
                    'permissions' => 'report.view',
                ],
            ],
        ]);

        // Removed: Posts, Media Library, and Modules functionality
        // $this->registerPostTypesInMenu(null);
        // Media Library and Modules menu items removed

        $this->addMenuItem([
            'label' => __('Monitoring'),
            'icon' => 'lucide:monitor',
            'id' => 'monitoring-submenu',
            'active' => Route::is('admin.actionlog.*'),
            'priority' => 50,
            'permissions' => 'actionlog.view',
            'children' => [
                [
                    'label' => __('Action Logs'),
                    'route' => route('admin.actionlog.index'),
                    'active' => Route::is('admin.actionlog.index'),
                    'priority' => 10,
                    'permissions' => 'actionlog.view',
                ],
                // Removed: Laravel Pulse - Not needed for healthcare application
            ],
        ], __('More'));

        $this->addMenuItem(
            [
                'label' => __('Access Control'),
                'icon' => 'lucide:key',
                'id' => 'access-control-submenu',
                'active' => Route::is('admin.roles.*') || Route::is('admin.permissions.*') || Route::is('admin.users.*'),
                'priority' => 30,
                'permissions' => ['role.create', 'role.view', 'role.edit', 'role.delete', 'user.create', 'user.view', 'user.edit', 'user.delete', 'permission.view'],
                'children' => [
                    [
                        'label' => __('Users'),
                        'route' => route('admin.users.index'),
                        'active' => Route::is('admin.users.index') || Route::is('admin.users.create') || Route::is('admin.users.edit'),
                        'priority' => 10,
                        'permissions' => 'user.view',
                    ],
                    [
                        'label' => __('Roles'),
                        'route' => route('admin.roles.index'),
                        'active' => Route::is('admin.roles.index') || Route::is('admin.roles.create') || Route::is('admin.roles.edit'),
                        'priority' => 20,
                        'permissions' => 'role.view',
                    ],
                    [
                        'label' => __('Permissions'),
                        'route' => route('admin.permissions.index'),
                        'active' => Route::is('admin.permissions.index') || Route::is('admin.permissions.show'),
                        'priority' => 30,
                        'permissions' => 'permission.view',
                    ],
                ],
            ],
            __('More')
        );

        $this->addMenuItem([
            'label' => __('Settings'),
            'icon' => 'lucide:settings',
            'id' => 'settings-submenu',
            'active' => Route::is('admin.settings.*') || Route::is('admin.translations.*'),
            'priority' => 40,
            'permissions' => ['settings.edit', 'translations.view'],
            'children' => [
                [
                    'label' => __('Settings'),
                    'route' => route('admin.settings.index'),
                    'active' => Route::is('admin.settings.index'),
                    'priority' => 20,
                    'permissions' => 'settings.edit',
                ],
                [
                    'label' => __('Translations'),
                    'route' => route('admin.translations.index'),
                    'active' => Route::is('admin.translations.*'),
                    'priority' => 10,
                    'permissions' => ['translations.view', 'translations.edit'],
                ],
            ],
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Logout'),
            'icon' => 'lucide:log-out',
            'route' => route('admin.dashboard'),
            'active' => false,
            'id' => 'logout',
            'priority' => 10000,
            'html' => '
                <li>
                    <form method="POST" action="' . route('admin.logout.submit') . '">
                        ' . csrf_field() . '
                        <button type="submit" class="menu-item group w-full text-left menu-item-inactive text-gray-700 dark:text-white hover:text-gray-700">
                            <iconify-icon icon="lucide:log-out" class="menu-item-icon " width="16" height="16"></iconify-icon>
                            <span class="menu-item-text">' . __('Logout') . '</span>
                        </button>
                    </form>
                </li>
            ',
        ], __('More'));

        $this->groups = Hook::applyFilters(AdminFilterHook::ADMIN_MENU_GROUPS_BEFORE_SORTING, $this->groups);

        $this->sortMenuItemsByPriority();

        return $this->applyFiltersToMenuItems();
    }

    /**
     * Register post types in the menu
     * Move to main group if $group is null
     */
    protected function registerPostTypesInMenu(?string $group = 'Content'): void
    {
        $contentService = app(ContentService::class);
        $postTypes = $contentService->getPostTypes();

        if ($postTypes->isEmpty()) {
            return;
        }

        foreach ($postTypes as $typeName => $type) {
            // Skip if not showing in menu.
            if (isset($type->show_in_menu) && ! $type->show_in_menu) {
                continue;
            }

            // Create children menu items.
            $children = [
                [
                    'title' => __("All {$type->label}"),
                    'route' => 'admin.posts.index',
                    'params' => $typeName,
                    'active' => request()->is('admin/posts/' . $typeName) ||
                        (request()->is('admin/posts/' . $typeName . '/*') && ! request()->is('admin/posts/' . $typeName . '/create')),
                    'priority' => 10,
                    'permissions' => 'post.view',
                ],
                [
                    'title' => __('Add New'),
                    'route' => 'admin.posts.create',
                    'params' => $typeName,
                    'active' => request()->is('admin/posts/' . $typeName . '/create'),
                    'priority' => 20,
                    'permissions' => 'post.create',
                ],
            ];

            // Add taxonomies as children of this post type if this post type has them.
            if (! empty($type->taxonomies)) {
                $taxonomies = $contentService->getTaxonomies()
                    ->whereIn('name', $type->taxonomies);

                foreach ($taxonomies as $taxonomy) {
                    $children[] = [
                        'title' => __($taxonomy->label),
                        'route' => 'admin.terms.index',
                        'params' => $taxonomy->name,
                        'active' => request()->is('admin/terms/' . $taxonomy->name . '*'),
                        'priority' => 30 + $taxonomy->id, // Prioritize after standard items
                        'permissions' => 'term.view',
                    ];
                }
            }

            // Set up menu item with all children.
            $menuItem = [
                'title' => __($type->label),
                'icon' => get_post_type_icon($typeName),
                'id' => 'post-type-' . $typeName,
                'active' => request()->is('admin/posts/' . $typeName . '*') ||
                    (! empty($type->taxonomies) && $this->isCurrentTermBelongsToPostType($type->taxonomies)),
                'priority' => 10,
                'permissions' => 'post.view',
                'children' => $children,
            ];

            $this->addMenuItem($menuItem, $group ?: __('Main'));
        }
    }

    /**
     * Check if the current term route belongs to the given taxonomies
     */
    protected function isCurrentTermBelongsToPostType(array $taxonomies): bool
    {
        if (! request()->is('admin/terms/*')) {
            return false;
        }

        // Get the current taxonomy from the route
        $currentTaxonomy = request()->segment(3); // admin/terms/{taxonomy}

        return in_array($currentTaxonomy, $taxonomies);
    }

    protected function sortMenuItemsByPriority(): void
    {
        foreach ($this->groups as &$groupItems) {
            usort($groupItems, function ($a, $b) {
                return (int) $a->priority <=> (int) $b->priority;
            });
        }
    }

    protected function applyFiltersToMenuItems(): array
    {
        $result = [];
        foreach ($this->groups as $group => $items) {
            // Filter items by permission.
            $filteredItems = array_filter($items, function (AdminMenuItem $item) {
                return $item->userHasPermission();
            });

            // Apply filters that might add/modify menu items.
            $filteredItems = Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU->value . strtolower((string) $group), $filteredItems);

            // Only add the group if it has items after filtering.
            if (! empty($filteredItems)) {
                $result[$group] = $filteredItems;
            }
        }

        return $result;
    }

    public function shouldExpandSubmenu(AdminMenuItem $menuItem): bool
    {
        // If the parent menu item is active, expand the submenu.
        if ($menuItem->active) {
            return true;
        }

        // Check if any child menu item is active.
        foreach ($menuItem->children as $child) {
            if ($child->active) {
                return true;
            }
        }

        return false;
    }

    public function render(array $groupItems): string
    {
        $html = '';
        foreach ($groupItems as $menuItem) {
            $filterKey = $menuItem->id ?? Str::slug($menuItem->label) ?: '';
            $html .= Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU_BEFORE->value . $filterKey, '');

            $html .= view('backend.layouts.partials.sidebar.menu-item', [
                'item' => $menuItem,
            ])->render();

            $html .= Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU_AFTER->value . $filterKey, '');
        }

        return $html;
    }
}
