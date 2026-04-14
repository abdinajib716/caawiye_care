<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CustomerDatatable extends Datatable
{
    public string $model = Customer::class;

    public bool $showFilters = false;
    public array $relationships = [];

    public array $searchableColumns = ['name', 'phone', 'address'];

    public array $filterableColumns = [
        'status' => ['active', 'inactive'],
        'country_code' => [
            '+252' => 'Somalia (+252)',
            '+254' => 'Kenya (+254)',
            '+251' => 'Ethiopia (+251)',
            '+256' => 'Uganda (+256)',
            '+255' => 'Tanzania (+255)',
            '+1' => 'USA/Canada (+1)',
            '+44' => 'UK (+44)',
            '+971' => 'UAE (+971)',
        ],
    ];

    public array $sortableColumns = [
        'name',
        'phone',
        'country_code',
        'status',
        'created_at',
        'updated_at',
    ];

    public array $bulkActions = [
        'activate' => 'Activate Selected',
        'deactivate' => 'Deactivate Selected',
        'delete' => 'Delete Selected',
    ];
    
    // Export/Import Configuration
    public bool $enablePdf = false;
    public bool $enablePrint = false;
    public bool $enableExport = true;
    public bool $enableImport = true;

    public function query(): Builder
    {
        return Customer::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    foreach ($this->searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->filters['status'] ?? null, function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when($this->filters['country_code'] ?? null, function (Builder $query, $countryCode) {
                $query->where('country_code', $countryCode);
            })
            ->orderBy($this->sort, $this->direction);
    }

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.customers.index',
            'create' => 'admin.customers.create',
            'show' => 'admin.customers.show',
            'edit' => 'admin.customers.edit',
            'export' => 'admin.customers.export',
            'import' => 'admin.customers.import',
            'sampleTemplate' => 'admin.customers.sample-template',
            'destroy' => 'admin.customers.destroy',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'customer.create',
            'view' => 'customer.view',
            'edit' => 'customer.edit',
            'delete' => 'customer.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Customer';
    }

    public function getModelNamePlural(): string
    {
        return 'Customers';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search customers...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Add Customer');
    }

    public function getNewResourceLinkRouteName(): string
    {
        return 'admin.customers.create';
    }

    public function getNewResourceLinkPermission(): string
    {
        return 'customer.create';
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Customer Name'),
                'sortable' => true,
                'searchable' => true,
                'sortBy' => 'name',
                'width' => 'w-2/5', // 40% width for customer name
            ],
            [
                'id' => 'phone',
                'title' => __('Phone'),
                'sortable' => true,
                'sortBy' => 'phone',
                'width' => 'w-1/5', // 20% width
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/12', // ~8% width
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'created_at',
                'title' => __('Created'),
                'sortable' => true,
                'sortBy' => 'created_at',
                'width' => 'w-1/8', // ~12% width
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'width' => 'w-1/12', // ~8% width
                'is_action' => true,
                'renderContent' => 'renderActionsColumn',
            ],
        ];
    }

    public function bulkActivate(): void
    {
        $this->authorize('update', Customer::class);

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('No Selection'),
                'message' => __('Please select customers to activate.'),
            ]);
            return;
        }

        $updatedCount = Customer::whereIn('id', $this->selectedItems)
            ->update(['status' => 'active']);

        $this->selectedItems = [];
        $this->dispatch('resetSelectedItems');

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Bulk Update Successful'),
            'message' => __(':count customers activated successfully.', ['count' => $updatedCount]),
        ]);

        $this->resetPage();
    }

    public function bulkDeactivate(): void
    {
        $this->authorize('update', Customer::class);

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('No Selection'),
                'message' => __('Please select customers to deactivate.'),
            ]);
            return;
        }

        $updatedCount = Customer::whereIn('id', $this->selectedItems)
            ->update(['status' => 'inactive']);

        $this->selectedItems = [];
        $this->dispatch('resetSelectedItems');

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Bulk Update Successful'),
            'message' => __(':count customers deactivated successfully.', ['count' => $updatedCount]),
        ]);

        $this->resetPage();
    }

    public function renderActionsColumn($customer): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        if (auth()->user()->can('view', $customer)) {
            $html .= '<a href="' . route('admin.customers.show', $customer) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Customer') . '">';
            $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
            $html .= '</a>';
        }

        // Edit button
        if (auth()->user()->can('update', $customer)) {
            $html .= '<a href="' . route('admin.customers.edit', $customer) . '" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Edit Customer') . '">';
            $html .= '<iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>';
            $html .= '</a>';
        }

        // Delete button with confirmation modal
        if (auth()->user()->can('delete', $customer)) {
            $html .= '<div x-data="{ deleteModalOpen: false }">';
            $html .= '<button @click="deleteModalOpen = true" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="' . __('Delete Customer') . '">';
            $html .= '<iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon>';
            $html .= '</button>';

            // Confirmation modal
            $html .= '<div x-cloak x-show="deleteModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="deleteModalOpen" x-on:keydown.esc.window="deleteModalOpen = false" x-on:click.self="deleteModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog" aria-modal="true">';
            $html .= '<div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-full max-w-md rounded-lg bg-white p-0 shadow-xl dark:bg-gray-800">';

            // Modal header
            $html .= '<div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-800">';
            $html .= '<h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">' . __('Delete Customer') . '</h3>';
            $html .= '<button x-on:click="deleteModalOpen = false" class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            $html .= '</div>';

            // Modal content
            $html .= '<div class="px-4 py-6 text-center">';
            $html .= '<p class="text-gray-500 dark:text-gray-300">' . __('Are you sure you want to delete this customer?') . '</p>';
            $html .= '<p class="font-medium text-gray-900 dark:text-white mt-2">' . e($customer->name) . '</p>';
            $html .= '<p class="text-sm text-gray-400 mt-1">' . __('This action cannot be undone.') . '</p>';
            $html .= '</div>';

            // Modal footer
            $html .= '<div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">';
            $html .= '<button type="button" x-on:click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">' . __('No, Cancel') . '</button>';
            $html .= '<button type="button" wire:click="deleteItem(' . $customer->id . ')" @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">' . __('Yes, Delete') . '</button>';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    public function renderStatusColumn($customer): string
    {
        $colorClass = match ($customer->status) {
            'active' => 'bg-green-100 text-green-800 border border-green-200',
            'inactive' => 'bg-red-100 text-red-800 border border-red-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">' . ucfirst($customer->status) . '</span>';
    }
}
